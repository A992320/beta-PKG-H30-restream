<?php
// orig 2717-2767

// ══ Categories Handlers (إدارة الأقسام) ══
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])){
    // CSRF Check
    $postedToken = $_POST['csrf_token'] ?? '';
    if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $postedToken)) {
        $_SESSION['error'] = 'انتهت صلاحية الجلسة، يرجى إعادة المحاولة.';
        header('Location: admin.php#categories');
        exit;
    }

    try {
        $name = htmlspecialchars(strip_tags($_POST['category_name']));
        $parent_id = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;
        $icon = htmlspecialchars(strip_tags($_POST['category_icon'] ?? 'fas fa-th-large'));
        $desc = htmlspecialchars(strip_tags($_POST['description'] ?? ''));
        
        $slug_new = "cat-".time()."-".rand(100,999);
        $pdo->prepare("INSERT INTO categories (name, parent_id, icon, description, slug) VALUES (?, ?, ?, ?, ?)")->execute([$name, $parent_id, $icon, $desc, $slug_new]);
        $_SESSION['success'] = '✅ تم إضافة القسم بنجاح.'; 
    } catch(PDOException $e) {
        error_log('[shashety] DB error: ' . $e->getMessage());
        $_SESSION['error'] = 'حدث خطأ في قاعدة البيانات، يرجى المحاولة مرة أخرى.';
    }
    header('Location: admin.php#categories'); 
    exit;
}

if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_category'])){
    // CSRF Check
    $postedToken = $_POST['csrf_token'] ?? '';
    if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $postedToken)) {
        $_SESSION['error'] = 'انتهت صلاحية الجلسة، يرجى إعادة المحاولة.';
        header('Location: admin.php#categories');
        exit;
    }

    try {
        $id = (int)$_POST['category_id'];
        $name = htmlspecialchars(strip_tags($_POST['category_name']));
        $parent_id = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;
        $icon = htmlspecialchars(strip_tags($_POST['category_icon'] ?? 'fas fa-th-large'));
        
        $pdo->prepare("UPDATE categories SET name=?, parent_id=?, icon=? WHERE id=?")->execute([$name, $parent_id, $icon, $id]);
        $_SESSION['success'] = '✅ تم تعديل القسم بنجاح.'; 
    } catch(PDOException $e) {
        error_log('[shashety] DB error: ' . $e->getMessage());
        $_SESSION['error'] = 'حدث خطأ في قاعدة البيانات، يرجى المحاولة مرة أخرى.';
    }
    header('Location: admin.php#categories'); 
    exit;
}

if(isset($_POST['delete_category']) && $_SERVER['REQUEST_METHOD'] === 'POST'){
    // CSRF Check
    $postedToken = $_POST['csrf_token'] ?? '';
    if (!csrfValidate($postedToken)) {
        $_SESSION['error'] = 'انتهت صلاحية الجلسة، يرجى إعادة المحاولة.';
        header('Location: admin.php#categories');
        exit;
    }

    $id = (int)$_POST['delete_category'];
    if ($id <= 0) {
        $_SESSION['error'] = 'معرّف القسم غير صالح.';
        header('Location: admin.php#categories');
        exit;
    }

    try {
        $exists = $pdo->prepare("SELECT id FROM categories WHERE id=?");
        $exists->execute([$id]);
        if (!$exists->fetchColumn()) {
            throw new RuntimeException('القسم غير موجود أو تم حذفه مسبقاً.');
        }
        // لا نحذف القنوات تلقائياً: يبقى المحتوى آمناً ويعرف المدير سبب منع الحذف.
        $count = $pdo->prepare("SELECT COUNT(*) FROM channels WHERE category_id=?");
        $count->execute([$id]);
        $channelsCount = (int)$count->fetchColumn();
        if ($channelsCount > 0) {
            throw new RuntimeException('لا يمكن حذف القسم لأنه يحتوي على '.$channelsCount.' قناة. انقل القنوات أو احذفها أولاً.');
        }
        // الأقسام الفرعية لا تُحذف؛ تصبح أقساماً مستقلة بعد حذف الأب.
        $pdo->prepare("UPDATE categories SET parent_id=NULL WHERE parent_id=?")->execute([$id]);
        $removed = $pdo->prepare("DELETE FROM categories WHERE id=?");
        $removed->execute([$id]);
        if ($removed->rowCount() !== 1) {
            throw new RuntimeException('تعذر حذف القسم من قاعدة البيانات.');
        }
        $_SESSION['success'] = '✅ تم حذف القسم بنجاح.';
    } catch(Throwable $e) {
        error_log('[shashety] category delete error: ' . $e->getMessage());
        $_SESSION['error'] = 'تعذر حذف القسم: '.$e->getMessage();
    }
    header('Location: admin.php#categories');
    exit;
}
// ══ Channels Handlers ══
