<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=UTF-8');
header('X-Content-Type-Options: nosniff');

const FEEDBACK_TO = 'info.cuviu@gmail.com';
const FEEDBACK_FROM = 'CuViu Scorebook Feedback <no-reply@cuviu.jp>';
const MAX_COMMENT_LENGTH = 4000;
const MAX_JSON_BYTES = 8000000;
const MAX_SCREENSHOT_BYTES = 8000000;
const FEEDBACK_STORE_DIR_NAME = 'scorebook-feedback';

function json_response(int $status, array $payload): void
{
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function text_length(string $value): int
{
    return function_exists('mb_strlen') ? mb_strlen($value, 'UTF-8') : strlen($value);
}

function text_substr(string $value, int $start, int $length): string
{
    return function_exists('mb_substr') ? mb_substr($value, $start, $length, 'UTF-8') : substr($value, $start, $length);
}

function text_contains_ci(string $haystack, string $needle): bool
{
    if (function_exists('mb_stripos')) {
        return mb_stripos($haystack, $needle, 0, 'UTF-8') !== false;
    }
    return stripos($haystack, $needle) !== false;
}

function post_value(string $key, int $maxLength = 500): string
{
    $value = isset($_POST[$key]) ? (string) $_POST[$key] : '';
    $value = str_replace(["\r", "\0"], '', $value);
    $value = trim($value);
    if (text_length($value) > $maxLength) {
        $value = text_substr($value, 0, $maxLength);
    }
    return $value;
}

function sanitize_header_value(string $value, int $maxLength = 120): string
{
    $value = preg_replace('/[\r\n]+/', ' ', $value) ?? '';
    $value = trim($value);
    if (text_length($value) > $maxLength) {
        $value = text_substr($value, 0, $maxLength);
    }
    return $value;
}

function sanitize_file_name(string $name, string $fallback): string
{
    $base = basename($name);
    $base = preg_replace('/[^A-Za-z0-9._-]+/', '_', $base) ?? '';
    $base = trim($base, '._-');
    return $base !== '' ? $base : $fallback;
}

function uploaded_file_payload(string $key, int $maxBytes, array $allowedMimes = []): ?array
{
    if (!isset($_FILES[$key]) || !is_array($_FILES[$key])) {
        return null;
    }
    $file = $_FILES[$key];
    $error = (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE);
    if ($error === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    if ($error !== UPLOAD_ERR_OK) {
        json_response(400, ['ok' => false, 'message' => '添付ファイルを読み込めませんでした。']);
    }
    $size = (int) ($file['size'] ?? 0);
    if ($size <= 0 || $size > $maxBytes) {
        json_response(400, ['ok' => false, 'message' => '添付ファイルのサイズが大きすぎます。']);
    }
    $tmpName = (string) ($file['tmp_name'] ?? '');
    if ($tmpName === '' || !is_uploaded_file($tmpName)) {
        json_response(400, ['ok' => false, 'message' => '添付ファイルを確認できませんでした。']);
    }
    $content = file_get_contents($tmpName);
    if ($content === false) {
        json_response(400, ['ok' => false, 'message' => '添付ファイルの読み取りに失敗しました。']);
    }
    $mime = (string) ($file['type'] ?? 'application/octet-stream');
    if (function_exists('finfo_open')) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        if ($finfo) {
            $detected = finfo_file($finfo, $tmpName);
            finfo_close($finfo);
            if (is_string($detected) && $detected !== '') {
                $mime = $detected;
            }
        }
    }
    if ($allowedMimes && !in_array($mime, $allowedMimes, true)) {
        json_response(400, ['ok' => false, 'message' => '添付できる画像形式は PNG / JPEG / WebP / GIF / HEIC です。']);
    }
    return [
        'name' => sanitize_file_name((string) ($file['name'] ?? ''), $key),
        'mime' => $mime,
        'content' => $content,
        'size' => $size,
    ];
}

function append_text_part(array &$parts, string $boundary, string $body): void
{
    $parts[] = "--{$boundary}\r\n"
        . "Content-Type: text/plain; charset=UTF-8\r\n"
        . "Content-Transfer-Encoding: 8bit\r\n\r\n"
        . $body . "\r\n";
}

function append_attachment_part(array &$parts, string $boundary, string $content, string $mime, string $filename): void
{
    $safeName = sanitize_file_name($filename, 'attachment.dat');
    $parts[] = "--{$boundary}\r\n"
        . "Content-Type: {$mime}; name=\"{$safeName}\"\r\n"
        . "Content-Disposition: attachment; filename=\"{$safeName}\"\r\n"
        . "Content-Transfer-Encoding: base64\r\n\r\n"
        . chunk_split(base64_encode($content)) . "\r\n";
}

function safe_path_component(string $value, string $fallback): string
{
    $value = preg_replace('/[^A-Za-z0-9_-]+/', '-', $value) ?? '';
    $value = trim($value, '-_');
    return $value !== '' ? $value : $fallback;
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

function ensure_feedback_store(): string
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

function feedback_category_rules(): array
{
    return [
        'bug' => ['バグ', '不具合', 'エラー', 'できない', '反応しない', '消え', 'ずれ', 'ズレ', 'おかしい', '変', '落ち', '失敗'],
        'runner' => ['走者', 'ランナー', '進塁', '盗塁', '牽制', '生還', 'タッチアップ', '暴投', '捕逸', 'アウト処理', '挟殺', '犠牲フライ'],
        'input' => ['入力', 'ボタン', 'タップ', '長押し', '押せ', '操作', '選択', 'ダイヤログ', 'フォーム'],
        'scorebook' => ['スコアブック', '記号', '早稲田', '慶応', '慶應', 'NPB', 'マニュアル', 'PDF資料', 'ボックス'],
        'roster' => ['選手', 'メンバー', 'スタメン', '投手', '代打', '代走', '守備', '背番号', 'DH'],
        'import' => ['取り込み', 'インポート', '日刊', 'X取り込み', 'Twitter', 'AI', 'OCR', '文字認識', '写真'],
        'share' => ['Xポスト', '下書き', 'シェア', '共有', '一言メモ', '呟', 'つぶやき'],
        'pdf' => ['PDF', '印刷', 'AirPrint', 'プリンタ', '出力'],
        'save' => ['保存', '復元', 'JSON', '読み込み', 'バックアップ', '自動保存'],
        'ads' => ['広告', 'AdMax', 'AdSense', '忍者', '課金', 'Pro'],
        'ui' => ['表示', '画面', 'レイアウト', 'デザイン', '右手', '左手', '配置', '見づらい'],
    ];
}

function classify_feedback(string $comment): array
{
    $matched = [];
    foreach (feedback_category_rules() as $category => $keywords) {
        foreach ($keywords as $keyword) {
            if (text_contains_ci($comment, $keyword)) {
                $matched[] = $category;
                break;
            }
        }
    }
    $matched = array_values(array_unique($matched));
    if (!$matched) {
        $matched[] = 'other';
    }
    return [
        'primary' => $matched[0],
        'tags' => $matched,
    ];
}

function append_jsonl(string $path, array $record): void
{
    $line = json_encode($record, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n";
    if (file_put_contents($path, $line, FILE_APPEND | LOCK_EX) === false) {
        throw new RuntimeException('Cannot append JSONL: ' . $path);
    }
    @chmod($path, 0600);
}

function extension_from_mime(string $mime): string
{
    $map = [
        'image/png' => 'png',
        'image/jpeg' => 'jpg',
        'image/webp' => 'webp',
        'image/gif' => 'gif',
        'image/heic' => 'heic',
        'image/heif' => 'heif',
    ];
    return $map[$mime] ?? 'bin';
}

function save_feedback_database_record(
    string $comment,
    array $classification,
    array $context,
    array $gameJson,
    ?array $screenshot,
    string $subject
): array {
    $root = ensure_feedback_store();
    $id = gmdate('Ymd_His') . '_' . bin2hex(random_bytes(4));
    $primary = safe_path_component((string) $classification['primary'], 'other');
    $jsonFile = 'attachments/' . $id . '.json';
    $jsonPath = $root . '/' . $jsonFile;
    if (file_put_contents($jsonPath, $gameJson['content'], LOCK_EX) === false) {
        throw new RuntimeException('Cannot write game JSON.');
    }
    @chmod($jsonPath, 0600);

    $screenshotFile = null;
    if ($screenshot) {
        $ext = extension_from_mime((string) $screenshot['mime']);
        $screenshotFile = 'attachments/' . $id . '.' . $ext;
        $screenshotPath = $root . '/' . $screenshotFile;
        if (file_put_contents($screenshotPath, $screenshot['content'], LOCK_EX) === false) {
            throw new RuntimeException('Cannot write screenshot.');
        }
        @chmod($screenshotPath, 0600);
    }

    $record = [
        'id' => $id,
        'receivedAt' => gmdate('c'),
        'primaryCategory' => $classification['primary'],
        'categories' => $classification['tags'],
        'subject' => $subject,
        'comment' => $comment,
        'context' => $context,
        'attachments' => [
            'gameJson' => $jsonFile,
            'screenshot' => $screenshotFile,
        ],
    ];

    append_jsonl($root . '/feedback.jsonl', $record);
    append_jsonl($root . '/by-category/' . $primary . '.jsonl', $record);

    $summaryPath = $root . '/summary.json';
    $summary = ['total' => 0, 'byCategory' => [], 'updatedAt' => gmdate('c')];
    if (is_file($summaryPath)) {
        $decoded = json_decode((string) file_get_contents($summaryPath), true);
        if (is_array($decoded)) {
            $summary = array_replace_recursive($summary, $decoded);
        }
    }
    $summary['total'] = ((int) ($summary['total'] ?? 0)) + 1;
    $summary['byCategory'][$classification['primary']] = ((int) ($summary['byCategory'][$classification['primary']] ?? 0)) + 1;
    $summary['updatedAt'] = gmdate('c');
    file_put_contents($summaryPath, json_encode($summary, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT), LOCK_EX);
    @chmod($summaryPath, 0600);

    return [
        'id' => $id,
        'root' => $root,
        'record' => $record,
    ];
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(405, ['ok' => false, 'message' => 'POSTで送信してください。']);
}

if (post_value('website', 200) !== '') {
    json_response(200, ['ok' => true, 'message' => '送信しました。']);
}

$comment = post_value('comment', MAX_COMMENT_LENGTH);
if ($comment === '') {
    json_response(400, ['ok' => false, 'message' => 'コメントを入力してください。']);
}

$gameJson = uploaded_file_payload('gameJson', MAX_JSON_BYTES);
if (!$gameJson) {
    json_response(400, ['ok' => false, 'message' => '試合データJSONが添付されていません。']);
}

$decodedGame = json_decode($gameJson['content'], true);
if (json_last_error() !== JSON_ERROR_NONE) {
    json_response(400, ['ok' => false, 'message' => '試合データJSONの形式を確認できませんでした。']);
}

$screenshot = uploaded_file_payload('screenshot', MAX_SCREENSHOT_BYTES, [
    'image/png',
    'image/jpeg',
    'image/webp',
    'image/gif',
    'image/heic',
    'image/heif',
]);

$appVersion = sanitize_header_value(post_value('appVersion', 40), 40);
$inning = sanitize_header_value(post_value('inning', 20), 20);
$halfLabel = sanitize_header_value(post_value('halfLabel', 10), 10);
$battingOrder = sanitize_header_value(post_value('battingOrder', 10), 10);
$teamName = sanitize_header_value(post_value('teamName', 80), 80);
$playerName = sanitize_header_value(post_value('playerName', 80), 80);
$scorebookStyle = sanitize_header_value(post_value('scorebookStyle', 30), 30);
$url = sanitize_header_value(post_value('url', 500), 500);
$userAgent = sanitize_header_value(post_value('userAgent', 500), 500);
$classification = classify_feedback($comment);

$subjectParts = ['[BBScore-FB]'];
if (!empty($classification['primary'])) {
    $subjectParts[] = '[' . sanitize_header_value((string) $classification['primary'], 30) . ']';
}
if ($appVersion !== '') {
    $subjectParts[] = 'v' . $appVersion;
}
if ($inning !== '') {
    $subjectParts[] = $inning . '回' . $halfLabel;
}
if ($battingOrder !== '') {
    $subjectParts[] = $battingOrder . '番';
}
$subject = implode(' ', $subjectParts);
$encodedSubject = function_exists('mb_encode_mimeheader')
    ? mb_encode_mimeheader($subject, 'UTF-8')
    : $subject;

$context = [
    'appVersion' => $appVersion,
    'scorebookStyle' => $scorebookStyle,
    'teamName' => $teamName,
    'inning' => $inning,
    'halfLabel' => $halfLabel,
    'battingOrder' => $battingOrder,
    'playerName' => $playerName !== '' ? $playerName : '未登録',
    'url' => $url,
    'userAgent' => $userAgent,
    'savedGameTitle' => is_array($decodedGame) ? (string) ($decodedGame['snapshot']['state']['teamNames']['top'] ?? '') . ' vs ' . (string) ($decodedGame['snapshot']['state']['teamNames']['bottom'] ?? '') : '',
];

$databaseStatus = 'not_saved';
$databaseId = '';
$databasePath = '';
try {
    $databaseResult = save_feedback_database_record($comment, $classification, $context, $gameJson, $screenshot, $subject);
    $databaseStatus = 'saved';
    $databaseId = (string) $databaseResult['id'];
    $databasePath = (string) $databaseResult['root'];
} catch (Throwable $error) {
    error_log('Scorebook feedback database save failed: ' . $error->getMessage());
    $databaseStatus = 'failed: ' . $error->getMessage();
}

$bodyLines = [
    'CuViu Scorebook 匿名フィードバック',
    '',
    '送信者名・メールアドレスは取得していません。',
    '',
    'Feedback Database:',
    '- ID: ' . ($databaseId !== '' ? $databaseId : '-'),
    '- Primary Category: ' . ($classification['primary'] ?? 'other'),
    '- Tags: ' . implode(', ', $classification['tags'] ?? ['other']),
    '- Store: ' . $databaseStatus,
    '- Store Path: ' . ($databasePath !== '' ? $databasePath : '-'),
    '',
    'コメント:',
    $comment,
    '',
    'Context:',
    '- App Version: ' . ($appVersion !== '' ? $appVersion : '-'),
    '- Style: ' . ($scorebookStyle !== '' ? $scorebookStyle : '-'),
    '- Team: ' . ($teamName !== '' ? $teamName : '-'),
    '- Inning: ' . ($inning !== '' ? $inning . '回' . $halfLabel : '-'),
    '- Batting Order: ' . ($battingOrder !== '' ? $battingOrder . '番' : '-'),
    '- Player: ' . ($playerName !== '' ? $playerName : '未登録'),
    '- URL: ' . ($url !== '' ? $url : '-'),
    '- User Agent: ' . ($userAgent !== '' ? $userAgent : '-'),
    '',
    '添付:',
    '- 試合データJSON: ' . $gameJson['name'] . ' (' . $gameJson['size'] . ' bytes)',
    '- スクリーンショット: ' . ($screenshot ? $screenshot['name'] . ' (' . $screenshot['size'] . ' bytes)' : 'なし'),
];
$body = implode("\r\n", $bodyLines);

$boundary = 'cuviu_feedback_' . bin2hex(random_bytes(12));
$parts = [];
append_text_part($parts, $boundary, $body);
append_attachment_part($parts, $boundary, $gameJson['content'], 'application/json', $gameJson['name']);
if ($screenshot) {
    append_attachment_part($parts, $boundary, $screenshot['content'], $screenshot['mime'], $screenshot['name']);
}
$parts[] = "--{$boundary}--\r\n";
$message = implode('', $parts);

$headers = [
    'From: ' . FEEDBACK_FROM,
    'MIME-Version: 1.0',
    'Content-Type: multipart/mixed; boundary="' . $boundary . '"',
    'X-Mailer: PHP/' . phpversion(),
];

$sent = mail(FEEDBACK_TO, $encodedSubject, $message, implode("\r\n", $headers));
if (!$sent) {
    json_response(500, ['ok' => false, 'message' => 'メール送信に失敗しました。']);
}

json_response(200, [
    'ok' => true,
    'message' => '送信しました。',
    'feedbackId' => $databaseId,
    'category' => $classification['primary'] ?? 'other',
    'databaseSaved' => $databaseId !== '',
]);
