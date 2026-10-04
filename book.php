<?php
/**
 * صفحة تفاصيل الكتاب - BookMart
 */
session_start();
require_once 'includes/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) {
    header('Location: books.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM books WHERE id = ? AND status = 'published'");
$stmt->execute([$id]);
$book = $stmt->fetch();

if (!$book) {
    header('Location: books.php');
    exit;
}

$page_title = $book['title'];
$body_class = '';
$condition_text = $book['condition'] === 'new' ? 'جديد' : 'مستعمل';

include 'includes/header.php';
?>
<div class="container">
    <div class="book-detail">
        <div class="book-detail-image">
            <?php 
            $img_src = !empty($book['image_url']) ? $book['image_url'] : (!empty($book['image']) && file_exists(__DIR__ . '/assets/images/' . $book['image']) ? 'assets/images/' . $book['image'] : '');
            if ($img_src): ?>
                <a href="<?php echo htmlspecialchars($img_src); ?>" target="_blank" rel="noopener" class="book-image-link" title="عرض الصورة بحجم كامل">
                    <img src="<?php echo htmlspecialchars($img_src); ?>" alt="<?php echo htmlspecialchars($book['title']); ?>">
                </a>
                <a href="<?php echo htmlspecialchars($img_src); ?>" download class="btn btn-outline btn-small" style="margin-top:0.5rem">تحميل صورة الغلاف</a>
            <?php else: ?>
                <div class="book-placeholder" style="height: 350px;">📚</div>
            <?php endif; ?>
        </div>
        <div class="book-detail-info">
            <h1 class="book-title"><?php echo htmlspecialchars($book['title']); ?></h1>
            <p><strong>المؤلف:</strong> <?php echo htmlspecialchars($book['author']); ?></p>
            <p><strong>الحالة:</strong> <span class="book-condition <?php echo $book['condition']; ?>"><?php echo $condition_text; ?></span></p>
            <p class="book-price" style="font-size: 1.3rem;"><?php echo number_format($book['price'], 2); ?> ر.س</p>
            <?php if (!empty($book['description'])): ?>
                <div class="book-description">
                    <strong>الوصف:</strong><br>
                    <?php echo nl2br(htmlspecialchars($book['description'])); ?>
                </div>
            <?php endif; ?>
            <div style="margin-top: 1.5rem;">
                <button type="button" class="btn btn-primary" onclick="addToCart(<?php echo $book['id']; ?>, '<?php echo addslashes(htmlspecialchars($book['title'])); ?>', <?php echo $book['price']; ?>, '<?php echo addslashes(!empty($book['image_url']) ? $book['image_url'] : ($book['image'] ?? '')); ?>')">إضافة للسلة</button>
            </div>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>
