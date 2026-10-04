<?php
/**
 * هيدر الموقع - BookMart
 * يحتوي على الشعار والقائمة الرئيسية
 */
require_once __DIR__ . '/asset_version.php';
$current_page = basename($_SERVER['PHP_SELF'], '.php');
// مسار الأساس للصفحات الفرعية
$base = (strpos($_SERVER['PHP_SELF'], '/pages/') !== false) ? '../' : '';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php require __DIR__ . '/theme-head.php'; ?>
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>BookMart</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo $base; ?>assets/css/style.css?v=<?php echo htmlspecialchars(BOOKMART_ASSET_VER, ENT_QUOTES, 'UTF-8'); ?>">
</head>
<body class="<?php
    $body_classes = ['site-body'];
    if (!empty($body_class)) {
        $body_classes[] = $body_class;
    }
    echo htmlspecialchars(implode(' ', $body_classes), ENT_QUOTES, 'UTF-8');
?>">
    <header class="site-header">
        <div class="container header-inner">
            <a href="<?php echo $base; ?>index.php" class="logo-link">
                <img src="<?php echo $base; ?>assets/images/Logo.jpeg" alt="BookMart" class="site-logo">
            </a>
            <div class="header-toolbar">
            <?php $theme_toggle_class = ''; require __DIR__ . '/theme-toggle.php'; ?>
            <nav class="main-nav">
                <a href="<?php echo $base; ?>index.php" class="<?php echo $current_page == 'index' ? 'active' : ''; ?>">الرئيسية</a>
                <a href="<?php echo $base; ?>books.php" class="<?php echo $current_page == 'books' ? 'active' : ''; ?>">جميع الكتب</a>
                <a href="<?php echo $base; ?>pages/about.php" class="<?php echo $current_page == 'about' ? 'active' : ''; ?>">نبذة عن الموقع</a>
                <a href="<?php echo $base; ?>cart.php" class="cart-link">السلة <span class="cart-count-badge cart-count" id="cart-badge" style="display:none">0</span></a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="<?php echo $base; ?>pages/profile.php" class="<?php echo $current_page == 'profile' ? 'active' : ''; ?>">الملف الشخصي</a>
                    <?php
                    $is_admin = false;
                    if (isset($_SESSION['user_id'])) {
                        $ar = $pdo->prepare("SELECT role FROM users WHERE id = ?");
                        $ar->execute([$_SESSION['user_id']]);
                        $is_admin = ($ar->fetch()['role'] ?? '') === 'admin';
                    }
                    if ($is_admin): ?>
                    <a href="<?php echo $base; ?>admin/">لوحة التحكم</a>
                    <?php endif; ?>
                    <a href="<?php echo $base; ?>logout.php">تسجيل الخروج</a>
                <?php else: ?>
                    <a href="<?php echo $base; ?>pages/login.php" class="<?php echo $current_page == 'login' ? 'active' : ''; ?>">تسجيل الدخول</a>
                    <a href="<?php echo $base; ?>pages/register.php" class="<?php echo $current_page == 'register' ? 'active' : ''; ?>">إنشاء حساب</a>
                <?php endif; ?>
            </nav>
            </div>
        </div>
    </header>
    <div class="welcome-banner" role="region" aria-label="ترحيب">
        <div class="container welcome-banner-inner">
            <p class="welcome-banner-ar"><span class="welcome-badge">ترحيب</span> مرحباً بكم في متجر <strong>BookMart</strong> — نتمنى لكم تجربة تسوق ممتعة وقراءة مفيدة.</p>
            <p class="welcome-banner-en" dir="ltr" lang="en"><span class="welcome-badge welcome-badge-en">Welcome</span> Welcome to <strong>BookMart</strong> — we wish you a pleasant shopping experience and happy reading.</p>
        </div>
    </div>
    <main class="main-content">
