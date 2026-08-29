<?php
declare(strict_types=1);

require_once __DIR__ . '/core/config.php';
require_once __DIR__ . '/site/functions/security_cache.php';
require_once __DIR__ . '/functions/api_subscriber_guard.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');

function subtitleProxyReply(array $payload, int $status = 200): void {
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}
function subtitleProxyError(string $message, int $status = 400): void {
    subtitleProxyReply(['success' => false, 'error' => $message], $status);
}
function subtitleProxyInput(): array {
    $raw = file_get_contents('php://input');
    if (is_string($raw) && $raw !== '') {
        $json = json_decode($raw, true);
        if (is_array($json)) return $json;
    }
    return is_array($_POST) ? $_POST : [];
}
function subtitleProxyRequest(string $url, string $method, array $headers, ?array $payload = null, int $timeout = 25): array {
    if (!function_exists('curl_init')) return [0, '', 'لا تدعم استضافة الموقع الاتصال بخدمة الترجمة.'];
    $curl = curl_init($url);
    curl_setopt_array($curl, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => $timeout,
        CURLOPT_CONNECTTIMEOUT => 8,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS => 2,
        CURLOPT_PROTOCOLS => CURLPROTO_HTTP | CURLPROTO_HTTPS,
        CURLOPT_REDIR_PROTOCOLS => CURLPROTO_HTTP | CURLPROTO_HTTPS,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,
        CURLOPT_HTTPHEADER => $headers,
    ]);
    if ($method === 'POST') {
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($payload ?? [], JSON_UNESCAPED_UNICODE));
    }
    $response = curl_exec($curl);
    $code = (int)curl_getinfo($curl, CURLINFO_HTTP_CODE);
    $error = (string)curl_error($curl);
    curl_close($curl);
    return [$code, is_string($response) ? $response : '', $error];
}
function subtitleProxyConfig(PDO $db): array {
    $keys = ['os_api_key', 'os_username', 'os_password'];
    $stmt = $db->query("SELECT setting_key, setting_value FROM settings WHERE setting_key IN ('os_api_key','os_username','os_password')");
    $found = [];
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) $found[(string)$row['setting_key']] = trim((string)$row['setting_value']);
    foreach ($keys as $key) if (empty($found[$key])) return [];
    return ['key' => $found['os_api_key'], 'user' => $found['os_username'], 'pass' => $found['os_password']];
}
function subtitleProxyTokenPath(): string {
    $dir = defined('CACHE_DIR') ? CACHE_DIR : __DIR__ . '/storage/cache';
    if (!is_dir($dir)) @mkdir($dir, 0750, true);
    return rtrim($dir, '/\\') . DIRECTORY_SEPARATOR . 'opensubtitles_public_token.json';
}
function subtitleProxyForgetToken(): void {
    $path = subtitleProxyTokenPath();
    if (is_file($path)) @unlink($path);
}
function subtitleProxyToken(array $config, bool $refresh = false): ?string {
    $path = subtitleProxyTokenPath();
    if (!$refresh && is_readable($path)) {
        $cached = json_decode((string)@file_get_contents($path), true);
        if (is_array($cached) && !empty($cached['token']) && (int)($cached['expires_at'] ?? 0) > time() + 120) {
            return (string)$cached['token'];
        }
    }
    [$code, $response, $error] = subtitleProxyRequest(
        'https://api.opensubtitles.com/api/v1/login',
        'POST',
        ['Content-Type: application/json', 'Accept: application/json', 'Api-Key: ' . $config['key'], 'User-Agent: ShashetyPRO v2.0'],
        ['username' => $config['user'], 'password' => $config['pass']]
    );
    if ($error !== '' || $code !== 200) return null;
    $data = json_decode($response, true);
    $token = is_array($data) ? trim((string)($data['token'] ?? '')) : '';
    if ($token === '') return null;
    $record = json_encode(['token' => $token, 'expires_at' => time() + 20 * 3600], JSON_UNESCAPED_SLASHES);
    @file_put_contents($path, $record, LOCK_EX);
    @chmod($path, 0600);
    return $token;
}
function subtitleProxyApi(array $config, string $path, string $method = 'GET', ?array $payload = null): array {
    $token = subtitleProxyToken($config);
    if ($token === null) return [0, '', 'تعذّر الاتصال بحساب الترجمة المحفوظ.'];
    $headers = ['Content-Type: application/json', 'Accept: application/json', 'Api-Key: ' . $config['key'], 'Authorization: Bearer ' . $token, 'User-Agent: ShashetyPRO v2.0'];
    [$code, $response, $error] = subtitleProxyRequest('https://api.opensubtitles.com/api/v1' . $path, $method, $headers, $payload);
    if ($code !== 401) return [$code, $response, $error];
    subtitleProxyForgetToken();
    $token = subtitleProxyToken($config, true);
    if ($token === null) return [401, '', 'تعذّر تجديد اتصال الترجمة.'];
    $headers[3] = 'Authorization: Bearer ' . $token;
    return subtitleProxyRequest('https://api.opensubtitles.com/api/v1' . $path, $method, $headers, $payload);
}
function subtitleProxyToVtt(string $content): string {
    $content = preg_replace('/^\xEF\xBB\xBF/', '', $content) ?? $content;
    if (!mb_check_encoding($content, 'UTF-8')) $content = mb_convert_encoding($content, 'UTF-8', 'Windows-1256, ISO-8859-6, Windows-1252');
    $content = str_replace(["\r\n", "\r"], "\n", $content);
    $content = preg_replace('/(\d{2}:\d{2}:\d{2}),(\d{3})/', '$1.$2', $content) ?? $content;
    return "WEBVTT\n\n" . trim($content) . "\n";
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') subtitleProxyError('طريقة الطلب غير مدعومة.', 405);
if (!shashety_csrf_check(false)) subtitleProxyError('انتهت جلسة الصفحة. حدّث الصفحة ثم أعد المحاولة.', 419);
apiSubscriberGuard('subtitles');

$input = subtitleProxyInput();
$action = (string)($input['action'] ?? '');
if (!in_array($action, ['search', 'load'], true)) subtitleProxyError('طلب ترجمة غير صالح.');
if (!rateLimit('subtitle_proxy:' . $action, $action === 'load' ? 3 : 12, $action === 'load' ? 300 : 60)) subtitleProxyError('الرجاء الانتظار قليلاً قبل طلب ترجمة أخرى.', 429);

try { $config = subtitleProxyConfig(db()); }
catch (Throwable $e) { subtitleProxyError('تعذّر قراءة إعدادات الترجمة.', 500); }
if (!$config) subtitleProxyError('لم تُضبط بيانات OpenSubtitles بعد.');

if ($action === 'search') {
    $title = trim((string)($input['title'] ?? ''));
    $language = strtolower(trim((string)($input['language'] ?? 'ar')));
    if ($title === '' || mb_strlen($title) > 180) subtitleProxyError('اسم الفيلم غير صالح.');
    if (!in_array($language, ['ar', 'en', 'tr'], true)) $language = 'ar';
    $query = http_build_query(['query' => $title, 'languages' => $language, 'order_by' => 'download_count', 'order_direction' => 'desc', 'per_page' => 10]);
    [$code, $response, $error] = subtitleProxyApi($config, '/subtitles?' . $query);
    if ($error !== '') subtitleProxyError('تعذّر الوصول إلى خدمة الترجمة.', 502);
    if ($code === 429) subtitleProxyError('خدمة الترجمة مشغولة الآن. حاول بعد دقيقة.', 429);
    if ($code !== 200) subtitleProxyError('تعذّر البحث عن ترجمات لهذا الفيلم.', 502);
    $data = json_decode($response, true);
    $items = [];
    foreach (($data['data'] ?? []) as $subtitle) {
        $attributes = is_array($subtitle['attributes'] ?? null) ? $subtitle['attributes'] : [];
        $file = $attributes['files'][0] ?? [];
        $fileId = (int)($file['file_id'] ?? 0);
        if ($fileId < 1) continue;
        $items[] = [
            'file_id' => $fileId,
            'title' => (string)($attributes['feature_details']['title'] ?? $attributes['release'] ?? 'ترجمة'),
            'release' => (string)($attributes['release'] ?? ''),
            'year' => (string)($attributes['feature_details']['year'] ?? ''),
            'language' => (string)($attributes['language'] ?? $language),
            'downloads' => (int)($attributes['download_count'] ?? 0),
        ];
    }
    subtitleProxyReply(['success' => true, 'items' => $items]);
}

$fileId = (int)($input['file_id'] ?? 0);
if ($fileId < 1) subtitleProxyError('معرّف ملف الترجمة غير صالح.');
[$code, $response, $error] = subtitleProxyApi($config, '/download', 'POST', ['file_id' => $fileId, 'sub_format' => 'srt']);
if ($error !== '') subtitleProxyError('تعذّر تجهيز ملف الترجمة.', 502);
$data = json_decode($response, true);
if ($code === 406) subtitleProxyError('تم استنفاد رصيد تنزيل الترجمات اليومي.', 429);
$link = is_array($data) ? trim((string)($data['link'] ?? '')) : '';
if ($code !== 200 || $link === '') subtitleProxyError('تعذّر تنزيل الترجمة المحددة.', 502);
$url = parse_url($link);
if (!is_array($url) || !in_array(strtolower((string)($url['scheme'] ?? '')), ['http', 'https'], true) || empty($url['host'])) subtitleProxyError('رابط الترجمة غير صالح.', 502);
[$downloadCode, $subtitle, $downloadError] = subtitleProxyRequest($link, 'GET', ['Accept: text/plain,*/*', 'User-Agent: ShashetyPRO v2.0'], null, 60);
if ($downloadError !== '' || $downloadCode < 200 || $downloadCode >= 300 || strlen(trim($subtitle)) < 8) subtitleProxyError('تعذّر قراءة ملف الترجمة.', 502);
if (strlen($subtitle) > 2 * 1024 * 1024) subtitleProxyError('ملف الترجمة كبير جداً للمشغل.', 413);
subtitleProxyReply(['success' => true, 'vtt' => subtitleProxyToVtt($subtitle)]);