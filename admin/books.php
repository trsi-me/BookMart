<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/asset_version.php';
require_once __DIR__ . '/includes/auth.php';

$search = trim($_GET['search'] ?? '');
$filter_status = $_GET['status'] ?? '';
$filter_condition = $_GET['condition'] ?? '';
$sort = $_GET['sort'] ?? 'newest';

$sql = "SELECT * FROM books WHERE 1=1";
$params = [];

if ($search) {
    $sql .= " AND (title LIKE ? OR author LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
if ($filter_status) {
    $sql .= " AND status = ?";
    $params[] = $filter_status;
}
if ($filter_condition) {
    $sql .= " AND `condition` = ?";
    $params[] = $filter_condition;
}

$sort_cols = ['newest' => 'created_at DESC', 'oldest' => 'created_at ASC', 'price_asc' => 'price ASC', 'price_desc' => 'price DESC', 'title' => 'title ASC'];
$sql .= " ORDER BY " . ($sort_cols[$sort] ?? 'created_at DESC');

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$books = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php require __DIR__ . '/../includes/theme-head.php'; ?>
    <title>إدارة الكتب - BookMart</title>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css?v=<?php echo htmlspecialchars(BOOKMART_ASSET_VER, ENT_QUOTES, 'UTF-8'); ?>">
    <link rel="stylesheet" href="assets/admin.css?v=<?php echo htmlspecialchars(BOOKMART_ASSET_VER, ENT_QUOTES, 'UTF-8'); ?>">
</head>
<body class="admin-body">
    <?php $admin_sidebar_active = 'books'; require __DIR__ . '/includes/sidebar.php'; ?>
    <main class="admin-main">
        <div class="admin-header">
            <h1>إدارة الكتب</h1>
            <a href="book-add.php" class="btn btn-primary">إضافة كتاب جديد</a>
        </div>

        <form method="GET" class="admin-search-bar">
            <input type="text" name="search" placeholder="بحث بالعنوان أو المؤلف..." value="<?php echo htmlspecialchars($search); ?>" style="flex:1;min-width:200px">
            <select name="status">
                <option value="">كل الحالات</option>
                <option value="published" <?php echo $filter_status === 'published' ? 'selected' : ''; ?>>منشور</option>
                <option value="draft" <?php echo $filter_status === 'draft' ? 'selected' : ''; ?>>مسودة</option>
                <option value="restricted" <?php echo $filter_status === 'restricted' ? 'selected' : ''; ?>>مقيد</option>
            </select>
            <select name="condition">
                <option value="">كل الأنواع</option>
                <option value="new" <?php echo $filter_condition === 'new' ? 'selected' : ''; ?>>جديد</option>
                <option value="used" <?php echo $filter_condition === 'used' ? 'selected' : ''; ?>>مستعمل</option>
            </select>
            <select name="sort">
                <option value="newest" <?php echo $sort === 'newest' ? 'selected' : ''; ?>>الأحدث</option>
                <option value="oldest" <?php echo $sort === 'oldest' ? 'selected' : ''; ?>>الأقدم</option>
                <option value="price_asc" <?php echo $sort === 'price_asc' ? 'selected' : ''; ?>>السعر: منخفض-عالي</option>
                <option value="price_desc" <?php echo $sort === 'price_desc' ? 'selected' : ''; ?>>السعر: عالي-منخفض</option>
                <option value="title" <?php echo $sort === 'title' ? 'selected' : ''; ?>>العنوان</option>
            </select>
            <button type="submit" class="btn btn-secondary">بحث</button>
        </form>

        <table class="admin-table">
            <thead>
                <tr>
                    <th>الصورة</th>
                    <th>العنوان</th>
                    <th>المؤلف</th>
                    <th>السعر</th>
                    <th>الحالة</th>
                    <th>النوع</th>
                    <th>إجراءات</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($books as $b): 
                    $status_txt = ['published' => 'منشور', 'draft' => 'مسودة', 'restricted' => 'مقيد'][$b['status']] ?? $b['status'];
                    $cond_txt = $b['condition'] === 'new' ? 'جديد' : 'مستعمل';
                    $img_src = !empty($b['image_url']) ? $b['image_url'] : (!empty($b['image']) && file_exists(__DIR__ . '/../assets/images/' . $b['image']) ? '../assets/images/' . $b['image'] : '');
                ?>
                <tr>
                    <td>
                        <?php if ($img_src): ?>
                            <img src="<?php echo htmlspecialchars($img_src); ?>" alt="">
                        <?php else: ?>
                            <div class="admin-thumb-placeholder">📚</div>
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($b['title']); ?></td>
                    <td><?php echo htmlspecialchars($b['author']); ?></td>
                    <td><?php echo number_format($b['price'], 2); ?> ر.س</td>
                    <td><span class="status-badge status-<?php echo $b['status']; ?>"><?php echo $status_txt; ?></span></td>
                    <td><?php echo $cond_txt; ?></td>
                    <td class="actions-cell">
                        <a href="../book.php?id=<?php echo $b['id']; ?>" class="btn btn-outline btn-small" target="_blank">عرض</a>
                        <a href="book-edit.php?id=<?php echo $b['id']; ?>" class="btn btn-secondary btn-small">تعديل</a>
                        <a href="book-delete.php?id=<?php echo $b['id']; ?>" class="btn btn-small" style="background:#e74c3c;color:#fff" onclick="return confirm('حذف هذا الكتاب؟')">حذف</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php if (empty($books)): ?>
            <p class="admin-empty-msg">لا توجد كتب</p>
        <?php endif; ?>
    </main>
    <script src="../assets/js/main.js?v=<?php echo htmlspecialchars(BOOKMART_ASSET_VER, ENT_QUOTES, 'UTF-8'); ?>"></script>
</body>
</html>
