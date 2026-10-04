<?php
/**
 * صفحة جميع الكتب - BookMart
 * عرض الكتب مع إمكانية الفلترة
 */
session_start();
require_once 'includes/db.php';

$page_title = 'جميع الكتب';
$body_class = '';

$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';
$search = trim($_GET['search'] ?? '');

$order = 'created_at DESC';
if ($sort === 'price_low') $order = 'price ASC';
elseif ($sort === 'price_high') $order = 'price DESC';
elseif ($sort === 'title') $order = 'title ASC';

$sql = "SELECT * FROM books WHERE status = 'published'";
$params = [];

if ($filter === 'new') {
    $sql .= " AND `condition` = 'new'";
} elseif ($filter === 'used') {
    $sql .= " AND `condition` = 'used'";
}

if ($search) {
    $sql .= " AND (title LIKE ? OR author LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$sql .= " ORDER BY $order";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$books = $stmt->fetchAll();

include 'includes/header.php';
?>
<div class="container">
    <h1>جميع الكتب</h1>

    <form method="GET" class="books-search-bar" style="margin-bottom:1.5rem">
        <input type="text" name="search" placeholder="بحث بالعنوان أو المؤلف..." value="<?php echo htmlspecialchars($search); ?>">
        <input type="hidden" name="filter" value="<?php echo htmlspecialchars($filter); ?>">
        <button type="submit" class="btn btn-secondary">بحث</button>
    </form>

    <div class="filters">
        <a href="books.php?filter=all&sort=<?php echo $sort; ?>&search=<?php echo urlencode($search); ?>" class="filter-btn <?php echo $filter === 'all' ? 'active' : ''; ?>">الكل</a>
        <a href="books.php?filter=new&sort=<?php echo $sort; ?>&search=<?php echo urlencode($search); ?>" class="filter-btn <?php echo $filter === 'new' ? 'active' : ''; ?>">كتب جديدة</a>
        <a href="books.php?filter=used&sort=<?php echo $sort; ?>&search=<?php echo urlencode($search); ?>" class="filter-btn <?php echo $filter === 'used' ? 'active' : ''; ?>">كتب مستعملة</a>
        <span style="margin-right:auto">|</span>
        <a href="books.php?filter=<?php echo $filter; ?>&sort=newest&search=<?php echo urlencode($search); ?>" class="filter-btn <?php echo $sort === 'newest' ? 'active' : ''; ?>">الأحدث</a>
        <a href="books.php?filter=<?php echo $filter; ?>&sort=price_low&search=<?php echo urlencode($search); ?>" class="filter-btn <?php echo $sort === 'price_low' ? 'active' : ''; ?>">السعر: منخفض</a>
        <a href="books.php?filter=<?php echo $filter; ?>&sort=price_high&search=<?php echo urlencode($search); ?>" class="filter-btn <?php echo $sort === 'price_high' ? 'active' : ''; ?>">السعر: عالي</a>
        <a href="books.php?filter=<?php echo $filter; ?>&sort=title&search=<?php echo urlencode($search); ?>" class="filter-btn <?php echo $sort === 'title' ? 'active' : ''; ?>">العنوان</a>
    </div>

    <div class="books-grid">
        <?php foreach ($books as $book): 
            $condition_text = $book['condition'] === 'new' ? 'جديد' : 'مستعمل';
            $cart_img = !empty($book['image_url']) ? $book['image_url'] : (!empty($book['image']) ? 'assets/images/' . $book['image'] : '');
        ?>
        <div class="book-item">
            <div class="book-item-image">
            <?php 
            $img_src = !empty($book['image_url']) ? $book['image_url'] : (!empty($book['image']) && file_exists(__DIR__ . '/assets/images/' . $book['image']) ? 'assets/images/' . $book['image'] : '');
            if ($img_src): ?>
                <img src="<?php echo htmlspecialchars($img_src); ?>" alt="<?php echo htmlspecialchars($book['title']); ?>" loading="lazy">
            <?php else: ?>
                <span>📚</span>
            <?php endif; ?>
            </div>
            <div class="book-item-content">
                <div class="book-title"><?php echo htmlspecialchars($book['title']); ?></div>
                <div class="book-author"><?php echo htmlspecialchars($book['author']); ?></div>
                <div class="book-item-meta">
                    <span class="book-price"><?php echo number_format($book['price'], 2); ?> ر.س</span>
                    <span class="book-condition <?php echo $book['condition']; ?>"><?php echo $condition_text; ?></span>
                </div>
                <div class="book-actions">
                    <a href="book.php?id=<?php echo $book['id']; ?>" class="btn btn-outline btn-small">عرض التفاصيل</a>
                    <button type="button" class="btn btn-primary btn-small" onclick="addToCart(<?php echo $book['id']; ?>, '<?php echo addslashes(htmlspecialchars($book['title'])); ?>', <?php echo $book['price']; ?>, '<?php echo addslashes($cart_img); ?>')">إضافة للسلة</button>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php include 'includes/footer.php'; ?>
