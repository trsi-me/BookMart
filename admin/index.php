<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/asset_version.php';
require_once __DIR__ . '/includes/auth.php';

$stmt = $pdo->query("SELECT COUNT(*) FROM books");
$books_count = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM users");
$users_count = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM orders");
$orders_count = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT SUM(total_price) FROM orders");
$revenue = $stmt->fetchColumn() ?: 0;
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php require __DIR__ . '/../includes/theme-head.php'; ?>
    <title>لوحة التحكم - BookMart</title>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css?v=<?php echo htmlspecialchars(BOOKMART_ASSET_VER, ENT_QUOTES, 'UTF-8'); ?>">
    <link rel="stylesheet" href="assets/admin.css?v=<?php echo htmlspecialchars(BOOKMART_ASSET_VER, ENT_QUOTES, 'UTF-8'); ?>">
</head>
<body class="admin-body">
    <?php $admin_sidebar_active = 'index'; require __DIR__ . '/includes/sidebar.php'; ?>
    <main class="admin-main">
        <div class="admin-header">
            <h1>لوحة التحكم</h1>
            <span>مرحباً، <?php echo htmlspecialchars($_SESSION['admin_name']); ?></span>
        </div>
        <div class="admin-stats">
            <div class="stat-card">
                <h3>عدد الكتب</h3>
                <div class="num"><?php echo $books_count; ?></div>
            </div>
            <div class="stat-card">
                <h3>المستخدمين</h3>
                <div class="num"><?php echo $users_count; ?></div>
            </div>
            <div class="stat-card">
                <h3>الطلبات</h3>
                <div class="num"><?php echo $orders_count; ?></div>
            </div>
            <div class="stat-card">
                <h3>الإيرادات (ر.س)</h3>
                <div class="num"><?php echo number_format($revenue, 2); ?></div>
            </div>
        </div>
    </main>
    <script src="../assets/js/main.js?v=<?php echo htmlspecialchars(BOOKMART_ASSET_VER, ENT_QUOTES, 'UTF-8'); ?>"></script>
</body>
</html>
