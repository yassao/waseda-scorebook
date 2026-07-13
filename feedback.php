<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=UTF-8');
header('X-Content-Type-Options: nosniff');

const FEEDBACK_TO = 'info.cuviu@gmail.com';
const FEEDBACK_FROM = 'CuViu Scorebook Feedback <no-reply@cuviu.jp>';
const MAX_COMMENT_LENGTH = 4000;
const MAX_JSON_BYTES = 8000000;
const MAX_SCREENSHOT_BYTES = 8000000;

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

json_decode($gameJson['content'], true);
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

$subjectParts = ['[BBScore-FB]'];
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

$bodyLines = [
    'CuViu Scorebook 匿名フィードバック',
    '',
    '送信者名・メールアドレスは取得していません。',
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

json_response(200, ['ok' => true, 'message' => '送信しました。']);
