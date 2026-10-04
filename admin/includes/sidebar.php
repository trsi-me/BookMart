<?php
/**
 * شريط الإدارة — عيّن $admin_sidebar_active قبل التضمين: index | books | add
 * صفحة تعديل الكتاب تستخدم books
 */
$active = isset($admin_sidebar_active) ? $admin_sidebar_active : '';
?>
<aside class="admin-sidebar">
    <div class="admin-sidebar-header">
        <div class="admin-brand">
            <strong>BookMart</strong>
            <small>لوحة التحكم</small>
        </div>
        <?php $theme_toggle_class = 'theme-toggle--compact'; require __DIR__ . '/../../includes/theme-toggle.php'; ?>
    </div>
    <a href="index.php" class="<?php echo $active === 'index' ? 'active' : ''; ?>">الرئيسية</a>
    <a href="books.php" class="<?php echo $active === 'books' ? 'active' : ''; ?>">إدارة الكتب</a>
    <a href="book-add.php" class="<?php echo $active === 'add' ? 'active' : ''; ?>">إضافة كتاب</a>
    <a href="../index.php">عرض الموقع</a>
    <a href="logout.php">تسجيل الخروج</a>
</aside>
