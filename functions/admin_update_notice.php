<?php
declare(strict_types=1);

/*
 * حالة تحديث لوحة الإدارة: فحص مخزّن مؤقتاً حتى لا يبطئ كل طلب إداري.
 * التجاوز مرتبط برقم الإصدار والقناة فقط؛ يظهر الشريط تلقائياً عند إصدار أحدث.
 */
function adminUpdateRoot(): string { return dirname(__DIR__); }
function adminUpdateCacheFile(): string { return adminUpdateRoot() . '/storage/cache/admin_update_notice.json'; }
function adminUpdateDismissFile(): string { return adminUpdateRoot() . '/storage/cache/admin_update_dismissed.json'; }

function adminUpdateReadJson(string $file): array {
    $raw = @file_get_contents($file);
    $data = is_string($raw) ? json_decode($raw, true) : null;
    return is_array($data) ? $data : [];
}
function adminUpdateWriteJson(string $file, array $data): void {
    $dir = dirname($file);
    if (!is_dir($dir)) @mkdir($dir, 0755, true);
    @file_put_contents($file, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), LOCK_EX);
}
function adminUpdateChannel(): string {
    $f = adminUpdateRoot() . '/channel.txt';
    $c = trim((string)@file_get_contents($f));
    return $c === 'testing' ? 'testing' : 'stable';
}
function adminUpdateSources(string $channel): array {
    return $channel === 'testing'
        ? [
            'version' => 'https://raw.githubusercontent.com/A992320/beta-PKG-H30-restream/main/version.txt',
            'log' => 'https://raw.githubusercontent.com/A992320/beta-PKG-H30-restream/main/whatsnew.txt',
            'label' => 'BETA',
          ]
        : [
            'version' => 'https://raw.githubusercontent.com/A992320/stable-PKG-H30-restream/main/version.txt',
            'log' => 'https://raw.githubusercontent.com/A992320/stable-PKG-H30-restream/main/whatsnew.txt',
            'label' => 'STABLE',
          ];
}
function adminUpdateFetch(string $url) {
    // تخطي الكاش الخاص بجيتهاب
    $url .= (strpos($url, '?') === false ? '?' : '&') . 't=' . time();
    
    $ctx = stream_context_create(['http' => [
        'method' => 'GET', 'timeout' => 4, 'follow_location' => true,
        'header' => "User-Agent: SHASHITY-Admin-Update-Check\r\n",
    ]]);
    return @file_get_contents($url, false, $ctx);
}
function adminUpdateLocalVersion(): string {
    $v = trim((string)@file_get_contents(adminUpdateRoot() . '/version.txt'));
    return $v !== '' ? $v : '0.0.0';
}
function adminUpdateLogLines(string $raw): array {
    $lines = preg_split('/\R/u', trim($raw)) ?: [];
    $out = [];
    foreach ($lines as $line) {
        $line = trim((string)$line);
        if ($line !== '') {
            $out[] = function_exists('mb_substr') ? mb_substr($line, 0, 500) : substr($line, 0, 500);
        }
        if (count($out) >= 40) break;
    }
    return $out;
}
function adminUpdateStatus(): array {
    if (session_status() === PHP_SESSION_NONE) {
        @session_start();
    }

    $channel = adminUpdateChannel();
    $local = adminUpdateLocalVersion();
    
    $cache = adminUpdateReadJson(adminUpdateCacheFile());
    $item = is_array($cache[$channel] ?? null) ? $cache[$channel] : [];

    $remote = (string)($item['remote'] ?? '');
    $available = $remote !== '' && version_compare($remote, $local, '>');
    
    $ignored = (($_SESSION['admin_update_dismissed'][$channel] ?? '') === $remote);

    return $status = [
        'available' => $available && !$ignored,
        'local' => $local,
        'remote' => $remote,
        'channel' => $channel,
        'label' => adminUpdateSources($channel)['label'],
        'log' => is_array($item['log'] ?? null) ? $item['log'] : [],
    ];
}

/* تجاوز نسخة محددة خلال الجلسة الحالية فقط: يظهر مجدداً عند تسجيل الدخول القادم. */
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST'
    && ($_POST['admin_update_notice_action'] ?? '') === 'dismiss') {
    if (!function_exists('csrfValidate') || !csrfValidate()) {
        http_response_code(403);
        exit('Invalid request');
    }
    $channel = adminUpdateChannel();
    $remote = trim((string)($_POST['remote_version'] ?? ''));
    if (preg_match('/^\d+(?:\.\d+){1,3}$/', $remote)) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['admin_update_dismissed'][$channel] = $remote;
    }
    header('Location: admin.php');
    exit;
}

/* تغيير القناة من الشريط مباشرة */
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST'
    && ($_POST['admin_update_notice_action'] ?? '') === 'change_channel') {
    if (!function_exists('csrfValidate') || !csrfValidate()) {
        http_response_code(403);
        exit('Invalid request');
    }
    $newChannel = ($_POST['new_channel'] ?? '') === 'testing' ? 'testing' : 'stable';
    @file_put_contents(adminUpdateRoot() . '/channel.txt', $newChannel, LOCK_EX);
    header('Location: admin.php');
    exit;
}

/* فحص التحديثات يدوياً عبر AJAX */
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST'
    && ($_POST['admin_update_notice_action'] ?? '') === 'check_updates') {
    header('Content-Type: application/json');
    if (!function_exists('csrfValidate') || !csrfValidate()) {
        echo json_encode(['status' => 'error', 'message' => 'CSRF Error']);
        exit;
    }
    $channel = adminUpdateChannel();
    $src = adminUpdateSources($channel);
    $remoteRaw = adminUpdateFetch($src['version']);
    if ($remoteRaw === false) {
        echo json_encode(['status' => 'error', 'message' => 'تعذر الاتصال بخادم التحديث (GitHub).']);
        exit;
    }
    $remote = trim($remoteRaw);
    $logRaw = adminUpdateFetch($src['log']);
    $item = [
        'remote' => preg_match('/^\d+(?:\.\d+){1,3}$/', $remote) ? $remote : '',
        'log' => $logRaw !== false ? adminUpdateLogLines((string)$logRaw) : [],
        'checked_at' => time(),
    ];
    $cache = adminUpdateReadJson(adminUpdateCacheFile());
    $cache[$channel] = $item;
    adminUpdateWriteJson(adminUpdateCacheFile(), $cache);

    $local = adminUpdateLocalVersion();
    $has_update = $item['remote'] !== '' && version_compare($item['remote'], $local, '>');

    echo json_encode([
        'status' => 'success',
        'has_update' => $has_update,
        'remote_version' => $item['remote']
    ]);
    exit;
}