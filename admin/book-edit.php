<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/asset_version.php';
require_once __DIR__ . '/includes/auth.php';

$id = (int)($_GET['id'] ?? 0);
if (!$id) { header('Location: books.php'); exit; }

$stmt = $pdo->prepare("SELECT * FROM books WHERE id = ?");
$stmt->execute([$id]);
$book = $stmt->fetch();
if (!$book) { header('Location: books.php'); exit; }

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $author = trim($_POST['author'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $condition = in_array($_POST['condition'] ?? '', ['new', 'used']) ? $_POST['condition'] : 'new';
    $status = in_array($_POST['status'] ?? '', ['draft', 'published', 'restricted']) ? $_POST['status'] : 'published';
    $description = trim($_POST['description'] ?? '');
    $image_url = trim($_POST['image_url'] ?? '');

    if (empty($title) || empty($author)) {
        $error = 'العنوان والمؤلف مطلوبان';
    } elseif ($price <= 0) {
        $error = 'السعر يجب أن يكون أكبر من صفر';
    } else {
        $stmt = $pdo->prepare("UPDATE books SET title=?, author=?, price=?, `condition`=?, status=?, description=?, image_url=? WHERE id=?");
        $stmt->execute([$title, $author, $price, $condition, $status, $description, $image_url ?: null, $id]);
        $success = 'تم تحديث الكتاب بنجاح';
        $book = array_merge($book, compact('title', 'author', 'price', 'condition', 'status', 'description', 'image_url'));
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php require __DIR__ . '/../includes/theme-head.php'; ?>
    <title>تعديل الكتاب - BookMart</title>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css?v=<?php echo htmlspecialchars(BOOKMART_ASSET_VER, ENT_QUOTES, 'UTF-8'); ?>">
    <link rel="stylesheet" href="assets/admin.css?v=<?php echo htmlspecialchars(BOOKMART_ASSET_VER, ENT_QUOTES, 'UTF-8'); ?>">
</head>
<body class="admin-body">
    <?php $admin_sidebar_active = 'books'; require __DIR__ . '/includes/sidebar.php'; ?>
    <main class="admin-main">
        <div class="admin-header">
            <h1>تعديل الكتاب</h1>
            <a href="books.php" class="btn btn-outline">رجوع</a>
        </div>

        <?php if ($error): ?><div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
        <?php if ($success): ?><div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div><?php endif; ?>

        <form method="POST" style="max-width:600px">
            <div class="form-group">
                <label>العنوان *</label>
                <input type="text" name="title" required value="<?php echo htmlspecialchars($book['title']); ?>">
            </div>
            <div class="form-group">
                <label>المؤلف *</label>
                <input type="text" name="author" required value="<?php echo htmlspecialchars($book['author']); ?>">
            </div>
            <div class="form-group">
                <label>السعر (ر.س) *</label>
                <input type="number" name="price" step="0.01" min="0" required value="<?php echo htmlspecialchars($book['price']); ?>">
            </div>
            <div class="form-group">
                <label>الحالة</label>
                <select name="condition">
                    <option value="new" <?php echo $book['condition'] === 'new' ? 'selected' : ''; ?>>جديد</option>
                    <option value="used" <?php echo $book['condition'] === 'used' ? 'selected' : ''; ?>>مستعمل</option>
                </select>
            </div>
            <div class="form-group">
                <label>حالة النشر</label>
                <select name="status">
                    <option value="published" <?php echo $book['status'] === 'published' ? 'selected' : ''; ?>>منشور</option>
                    <option value="draft" <?php echo $book['status'] === 'draft' ? 'selected' : ''; ?>>مسودة</option>
                    <option value="restricted" <?php echo $book['status'] === 'restricted' ? 'selected' : ''; ?>>مقيد</option>
                </select>
            </div>
            <div class="form-group">
                <label>رابط صورة الغلاف</label>
                <input type="url" name="image_url" value="<?php echo htmlspecialchars($book['image_url'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>الوصف</label>
                <textarea name="description" rows="4"><?php echo htmlspecialchars($book['description'] ?? ''); ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">حفظ التعديلات</button>
        </form>
    </main>
    <script src="../assets/js/main.js?v=<?php echo htmlspecialchars(BOOKMART_ASSET_VER, ENT_QUOTES, 'UTF-8'); ?>"></script>
</body>
</html>
