<?php
declare(strict_types=1);

const FEEDBACK_STORE_DIR_NAME = 'scorebook-feedback';
const ADMIN_TOKEN_FILE = 'admin-token.txt';
const ADMIN_COOKIE = 'cuviu_feedback_admin';
const MAX_RECORDS = 500;

function h(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function text_starts_with(string $haystack, string $needle): bool
{
    return $needle === '' || strpos($haystack, $needle) === 0;
}

function text_ends_with(string $haystack, string $needle): bool
{
    return $needle === '' || substr($haystack, -strlen($needle)) === $needle;
}

function text_contains(string $haystack, string $needle): bool
{
    return $needle === '' || strpos($haystack, $needle) !== false;
}

function feedback_store_root(): string
{
    $publicRoot = dirname(__DIR__, 2);
    if (basename($publicRoot) === 'public_html') {
        return dirname($publicRoot) . '/' . FEEDBACK_STORE_DIR_NAME;
    }
    return __DIR__ . '/feedback_store';
}

function ensure_directory(string $path): void
{
    if (is_dir($path)) {
        return;
    }
    if (!mkdir($path, 0700, true) && !is_dir($path)) {
        throw new RuntimeException('Cannot create directory: ' . $path);
    }
}

function ensure_store(): string
{
    $root = feedback_store_root();
    ensure_directory($root);
    ensure_directory($root . '/attachments');
    ensure_directory($root . '/by-category');
    @chmod($root, 0700);
    @chmod($root . '/attachments', 0700);
    @chmod($root . '/by-category', 0700);
    if (strpos($root, __DIR__) === 0) {
        @file_put_contents($root . '/.htaccess', "Require all denied\nDeny from all\n");
        @file_put_contents($root . '/index.html', '');
    }
    return $root;
}

function ensure_admin_token(string $root): string
{
    $path = $root . '/' . ADMIN_TOKEN_FILE;
    if (!is_file($path) || trim((string) file_get_contents($path)) === '') {
        $token = bin2hex(random_bytes(24));
        if (file_put_contents($path, $token . "\n", LOCK_EX) === false) {
            throw new RuntimeException('Cannot write admin token.');
        }
        @chmod($path, 0600);
    }
    return trim((string) file_get_contents($path));
}

function request_value(string $key, int $maxLength = 500): string
{
    $value = isset($_REQUEST[$key]) ? (string) $_REQUEST[$key] : '';
    $value = str_replace(["\r", "\n", "\0"], '', $value);
    $value = trim($value);
    if (strlen($value) > $maxLength) {
        $value = substr($value, 0, $maxLength);
    }
    return $value;
}

function is_https(): bool
{
    return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
}

function set_admin_cookie(string $token, int $expires = 0): void
{
    setcookie(ADMIN_COOKIE, $token, [
        'expires' => $expires ?: time() + 60 * 60 * 24 * 30,
        'path' => dirname((string) ($_SERVER['SCRIPT_NAME'] ?? '/')) ?: '/',
        'secure' => is_https(),
        'httponly' => true,
        'samesite' => 'Strict',
    ]);
}

function provided_token(): string
{
    $token = request_value('token', 120);
    if ($token !== '') {
        return $token;
    }
    return isset($_COOKIE[ADMIN_COOKIE]) ? trim((string) $_COOKIE[ADMIN_COOKIE]) : '';
}

function is_authorized(string $expectedToken): bool
{
    $token = provided_token();
    return $token !== '' && hash_equals($expectedToken, $token);
}

function read_json_file(string $path): array
{
    if (!is_file($path)) {
        return [];
    }
    $decoded = json_decode((string) file_get_contents($path), true);
    return is_array($decoded) ? $decoded : [];
}

function read_feedback_records(string $root): array
{
    $path = $root . '/feedback.jsonl';
    if (!is_file($path)) {
        return [];
    }
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if (!is_array($lines)) {
        return [];
    }
    $records = [];
    foreach ($lines as $line) {
        $decoded = json_decode($line, true);
        if (is_array($decoded) && isset($decoded['id'])) {
            $records[] = $decoded;
        }
    }
    return array_reverse($records);
}

function record_matches(array $record, string $category, string $query): bool
{
    if ($category !== '') {
        $categories = $record['categories'] ?? [];
        if (!is_array($categories)) {
            $categories = [];
        }
        if (($record['primaryCategory'] ?? '') !== $category && !in_array($category, $categories, true)) {
            return false;
        }
    }
    if ($query !== '') {
        $haystack = implode("\n", [
            (string) ($record['id'] ?? ''),
            (string) ($record['comment'] ?? ''),
            (string) ($record['subject'] ?? ''),
            json_encode($record['context'] ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '',
            implode(',', is_array($record['categories'] ?? null) ? $record['categories'] : []),
        ]);
        if (function_exists('mb_stripos')) {
            return mb_stripos($haystack, $query, 0, 'UTF-8') !== false;
        }
        return stripos($haystack, $query) !== false;
    }
    return true;
}

function find_record(array $records, string $id): ?array
{
    foreach ($records as $record) {
        if ((string) ($record['id'] ?? '') === $id) {
            return $record;
        }
    }
    return null;
}

function safe_attachment_path(string $root, ?string $relative): ?string
{
    if (!$relative || text_contains($relative, '..') || text_starts_with($relative, '/')) {
        return null;
    }
    $path = $root . '/' . $relative;
    $realRoot = realpath($root . '/attachments');
    $realPath = realpath($path);
    if (!$realRoot || !$realPath || !text_starts_with($realPath, $realRoot . DIRECTORY_SEPARATOR)) {
        return null;
    }
    return is_file($realPath) ? $realPath : null;
}

function content_type_for_file(string $path): string
{
    if (function_exists('finfo_open')) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        if ($finfo) {
            $mime = finfo_file($finfo, $path);
            finfo_close($finfo);
            if (is_string($mime) && $mime !== '') {
                return $mime;
            }
        }
    }
    return text_ends_with($path, '.json') ? 'application/json' : 'application/octet-stream';
}

function send_attachment(string $root, array $records): void
{
    $id = request_value('id', 80);
    $type = request_value('type', 30);
    if ($id === '' || $type === '') {
        return;
    }
    $record = find_record($records, $id);
    if (!$record) {
        http_response_code(404);
        echo 'Not found';
        exit;
    }
    $attachments = is_array($record['attachments'] ?? null) ? $record['attachments'] : [];
    $relative = $type === 'screenshot'
        ? (string) ($attachments['screenshot'] ?? '')
        : (string) ($attachments['gameJson'] ?? '');
    $path = safe_attachment_path($root, $relative);
    if (!$path) {
        http_response_code(404);
        echo 'Not found';
        exit;
    }
    header('Content-Type: ' . content_type_for_file($path));
    header('Content-Length: ' . (string) filesize($path));
    if (request_value('inline', 10) !== '1') {
        header('Content-Disposition: attachment; filename="' . basename($path) . '"');
    }
    readfile($path);
    exit;
}

function category_label(string $category): string
{
    $labels = [
        'bug' => '不具合',
        'runner' => '走者',
        'input' => '入力',
        'scorebook' => '記号',
        'roster' => '選手',
        'import' => '取込',
        'share' => '共有',
        'pdf' => 'PDF',
        'save' => '保存',
        'ads' => '広告',
        'ui' => 'UI',
        'other' => 'その他',
    ];
    return $labels[$category] ?? $category;
}

function build_query(array $params): string
{
    $base = [
        'category' => request_value('category', 80),
        'q' => request_value('q', 120),
    ];
    foreach ($params as $key => $value) {
        if ($value === null) {
            unset($base[$key]);
        } else {
            $base[$key] = $value;
        }
    }
    return http_build_query(array_filter($base, static function ($value): bool {
        return $value !== '';
    }));
}

$root = ensure_store();
$adminToken = ensure_admin_token($root);

if (request_value('logout', 5) === '1') {
    set_admin_cookie('', time() - 3600);
    header('Location: feedback_admin.php');
    exit;
}

$authorized = is_authorized($adminToken);
if ($authorized) {
    set_admin_cookie($adminToken);
}

if ($authorized && (request_value('download', 5) === '1' || request_value('inline', 10) === '1')) {
    send_attachment($root, read_feedback_records($root));
}

if (!$authorized) {
    http_response_code(401);
    $invalid = request_value('token', 120) !== '';
    ?>
<!doctype html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>フィードバック台帳ログイン</title>
    <style>
        body{margin:0;min-height:100vh;display:grid;place-items:center;background:#eaf6ff;color:#172638;font-family:-apple-system,BlinkMacSystemFont,"Hiragino Sans","Yu Gothic",sans-serif}
        main{width:min(560px,calc(100% - 32px));background:#fff;border:2px solid #d7e5ef;border-radius:24px;padding:28px;box-shadow:0 18px 50px rgba(23,38,56,.14)}
        h1{margin:0 0 12px;font-size:28px}.note{color:#607184;line-height:1.7;font-weight:700}label{display:block;margin-top:20px;font-weight:900}
        input{box-sizing:border-box;width:100%;margin-top:8px;padding:14px 16px;border:2px solid #cfdce7;border-radius:14px;font-size:16px}
        button{width:100%;margin-top:18px;padding:14px 16px;border:0;border-radius:14px;background:#0f7f72;color:#fff;font-size:18px;font-weight:900}
        .error{margin-top:12px;color:#c82f2f;font-weight:900}.path{font-family:ui-monospace,SFMono-Regular,Menlo,monospace;font-size:12px;background:#f4f8fb;padding:10px;border-radius:10px}
    </style>
</head>
<body>
<main>
    <h1>フィードバック台帳</h1>
    <p class="note">匿名フィードバックの試合データJSONとスクリーンショットを扱うため、管理トークンで保護しています。</p>
    <?php if ($invalid): ?><div class="error">トークンが違います。</div><?php endif; ?>
    <form method="get">
        <label for="token">管理トークン</label>
        <input id="token" name="token" type="password" autocomplete="current-password" required>
        <button type="submit">台帳を開く</button>
    </form>
    <p class="note">トークン保存先:</p>
    <div class="path"><?= h($root . '/' . ADMIN_TOKEN_FILE) ?></div>
</main>
</body>
</html>
    <?php
    exit;
}

$summary = read_json_file($root . '/summary.json');
$records = read_feedback_records($root);
$category = request_value('category', 80);
$query = request_value('q', 120);
$filtered = array_values(array_filter($records, static function ($record) use ($category, $query): bool {
    return is_array($record) && record_matches($record, $category, $query);
}));
$visible = array_slice($filtered, 0, MAX_RECORDS);
$byCategory = is_array($summary['byCategory'] ?? null) ? $summary['byCategory'] : [];
arsort($byCategory);
?>
<!doctype html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title>Scorebook Feedback</title>
    <style>
        :root{--ink:#172638;--muted:#607184;--line:#d7e5ef;--field:#0f7f72;--sky:#eaf6ff;--paper:#fff}
        *{box-sizing:border-box}body{margin:0;background:linear-gradient(180deg,#eff9ff,#dcefff);color:var(--ink);font-family:-apple-system,BlinkMacSystemFont,"Hiragino Sans","Yu Gothic",sans-serif;font-weight:700}
        header{position:sticky;top:0;z-index:2;background:rgba(255,255,255,.92);backdrop-filter:blur(14px);border-bottom:1px solid var(--line)}
        .wrap{width:min(1120px,100%);margin:0 auto;padding:18px 16px}h1{margin:0;font-size:28px;line-height:1.2}.top{display:flex;gap:12px;align-items:center;justify-content:space-between;flex-wrap:wrap}
        .muted{color:var(--muted)}.stats{display:flex;gap:8px;flex-wrap:wrap;margin-top:14px}.pill{display:inline-flex;align-items:center;gap:6px;padding:8px 11px;border:1px solid var(--line);border-radius:999px;background:#fff;color:var(--ink);text-decoration:none;font-size:13px}
        .pill.is-active{background:#e6fff8;border-color:#72c9ba;color:#075f55}.filters{display:grid;grid-template-columns:1fr auto;gap:10px;margin-top:14px}.filters input{width:100%;padding:12px;border:2px solid var(--line);border-radius:14px;font:inherit}.filters button,.link-btn{padding:12px 16px;border:0;border-radius:14px;background:var(--field);color:#fff;font:inherit;text-decoration:none}
        main.wrap{padding-top:18px}.record{background:var(--paper);border:1px solid var(--line);border-radius:22px;padding:16px;margin-bottom:14px;box-shadow:0 10px 24px rgba(23,38,56,.08)}
        .record-head{display:flex;justify-content:space-between;gap:10px;flex-wrap:wrap;border-bottom:1px solid #edf3f7;padding-bottom:10px;margin-bottom:12px}.id{font-family:ui-monospace,SFMono-Regular,Menlo,monospace;font-size:13px}.comment{white-space:pre-wrap;line-height:1.75;font-size:17px;margin:12px 0}
        .grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:8px;margin-top:12px}.kv{background:#f6fafc;border-radius:12px;padding:9px 10px;min-height:48px}.kv small{display:block;color:var(--muted);font-size:11px}.kv span{display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
        .actions{display:flex;gap:10px;flex-wrap:wrap;margin-top:12px}.actions a,.copy-note{display:inline-flex;align-items:center;justify-content:center;min-height:38px;padding:8px 12px;border:1px solid var(--line);border-radius:12px;background:#fff;color:var(--ink);text-decoration:none;font-weight:900}
        .copy-note{width:100%;justify-content:flex-start;color:var(--muted);font-size:13px}.thumb{max-width:240px;max-height:180px;border:1px solid var(--line);border-radius:12px;margin-top:10px;object-fit:contain;background:#f8fbfd}
        .empty{padding:28px;background:#fff;border:1px dashed var(--line);border-radius:22px;color:var(--muted)}.logout{color:var(--muted);text-decoration:none}
        @media(max-width:760px){.filters{grid-template-columns:1fr}.grid{grid-template-columns:repeat(2,minmax(0,1fr))}.record{border-radius:18px}.wrap{padding-left:12px;padding-right:12px}h1{font-size:24px}}
    </style>
</head>
<body>
<header>
    <div class="wrap">
        <div class="top">
            <div>
                <h1>フィードバック台帳</h1>
                <div class="muted">全<?= h((string) count($records)) ?>件 / 表示<?= h((string) count($visible)) ?>件<?= count($filtered) > count($visible) ? '（最新' . MAX_RECORDS . '件まで）' : '' ?></div>
            </div>
            <a class="logout" href="?logout=1">ログアウト</a>
        </div>
        <div class="stats">
            <a class="pill<?= $category === '' ? ' is-active' : '' ?>" href="?<?= h(build_query(['category' => null])) ?>">全部 <?= h((string) count($records)) ?></a>
            <?php foreach ($byCategory as $cat => $count): ?>
                <a class="pill<?= $category === (string) $cat ? ' is-active' : '' ?>" href="?<?= h(build_query(['category' => (string) $cat])) ?>"><?= h(category_label((string) $cat)) ?> <?= h((string) $count) ?></a>
            <?php endforeach; ?>
        </div>
        <form class="filters" method="get">
            <?php if ($category !== ''): ?><input type="hidden" name="category" value="<?= h($category) ?>"><?php endif; ?>
            <input name="q" value="<?= h($query) ?>" placeholder="ID、コメント、選手名、カテゴリで検索">
            <button type="submit">検索</button>
        </form>
    </div>
</header>
<main class="wrap">
    <?php if (!$visible): ?>
        <div class="empty">まだ表示できるフィードバックがありません。</div>
    <?php endif; ?>
    <?php foreach ($visible as $record): ?>
        <?php
        $id = (string) ($record['id'] ?? '');
        $context = is_array($record['context'] ?? null) ? $record['context'] : [];
        $attachments = is_array($record['attachments'] ?? null) ? $record['attachments'] : [];
        $screenshot = (string) ($attachments['screenshot'] ?? '');
        $tags = is_array($record['categories'] ?? null) ? $record['categories'] : [];
        $codexNote = 'FB ' . $id . ' を確認して対応してください。';
        ?>
        <article class="record">
            <div class="record-head">
                <div>
                    <div class="id"><?= h($id) ?></div>
                    <div class="muted"><?= h((string) ($record['receivedAt'] ?? '')) ?></div>
                </div>
                <div class="stats">
                    <?php foreach ($tags as $tag): ?><span class="pill"><?= h(category_label((string) $tag)) ?></span><?php endforeach; ?>
                </div>
            </div>
            <div class="comment"><?= h((string) ($record['comment'] ?? '')) ?></div>
            <div class="grid">
                <div class="kv"><small>場面</small><span><?= h((string) ($context['inning'] ?? '-')) ?>回<?= h((string) ($context['halfLabel'] ?? '')) ?> <?= h((string) ($context['battingOrder'] ?? '-')) ?>番</span></div>
                <div class="kv"><small>選手</small><span><?= h((string) ($context['playerName'] ?? '-')) ?></span></div>
                <div class="kv"><small>チーム</small><span><?= h((string) ($context['teamName'] ?? '-')) ?></span></div>
                <div class="kv"><small>Version</small><span><?= h((string) ($context['appVersion'] ?? '-')) ?></span></div>
                <div class="kv"><small>方式</small><span><?= h((string) ($context['scorebookStyle'] ?? '-')) ?></span></div>
                <div class="kv"><small>URL</small><span><?= h((string) ($context['url'] ?? '-')) ?></span></div>
                <div class="kv"><small>User Agent</small><span><?= h((string) ($context['userAgent'] ?? '-')) ?></span></div>
                <div class="kv"><small>件名</small><span><?= h((string) ($record['subject'] ?? '-')) ?></span></div>
            </div>
            <div class="actions">
                <a href="?download=1&type=json&id=<?= h(rawurlencode($id)) ?>">試合JSON</a>
                <?php if ($screenshot !== ''): ?>
                    <a href="?download=1&type=screenshot&id=<?= h(rawurlencode($id)) ?>">スクショ</a>
                    <a href="?inline=1&type=screenshot&id=<?= h(rawurlencode($id)) ?>" target="_blank" rel="noopener">画像表示</a>
                <?php endif; ?>
                <span class="copy-note"><?= h($codexNote) ?></span>
            </div>
            <?php if ($screenshot !== ''): ?>
                <img class="thumb" src="?inline=1&type=screenshot&id=<?= h(rawurlencode($id)) ?>" alt="feedback screenshot">
            <?php endif; ?>
        </article>
    <?php endforeach; ?>
</main>
</body>
</html>
