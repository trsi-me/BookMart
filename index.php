<?php
/**
 * الصفحة الرئيسية - BookMart
 * عرض كتب مميزة وجديدة ومستعملة
 */
session_start();
require_once 'includes/db.php';

$page_title = 'الرئيسية';
$body_class = 'home-page';

// جلب الكتب المميزة (أحدث 6 كتب منشورة فقط)
$stmt = $pdo->prepare("SELECT * FROM books WHERE status = 'published' ORDER BY created_at DESC LIMIT 6");
$stmt->execute();
$featured_books = $stmt->fetchAll();

// جلب كتب جديدة
$stmt = $pdo->prepare("SELECT * FROM books WHERE `condition` = 'new' AND status = 'published' ORDER BY created_at DESC LIMIT 6");
$stmt->execute();
$new_books = $stmt->fetchAll();

// جلب كتب مستعملة
$stmt = $pdo->prepare("SELECT * FROM books WHERE `condition` = 'used' AND status = 'published' ORDER BY created_at DESC LIMIT 6");
$stmt->execute();
$used_books = $stmt->fetchAll();

include 'includes/header.php';
?>
<div class="container">
    <h1 class="page-heading" style="text-align: center; margin-bottom: 2rem;">اكتشف مكتبتنا</h1>

    <!-- كتب مميزة -->
    <section class="books-section">
        <h2 class="section-title">كتب مميزة</h2>
        <div class="books-grid">
            <?php foreach ($featured_books as $book): 
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
    </section>

    <!-- كتب جديدة -->
    <section class="books-section">
        <h2 class="section-title">كتب جديدة</h2>
        <div class="books-grid">
            <?php foreach ($new_books as $book): 
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
    </section>

    <!-- كتب مستعملة -->
    <section class="books-section">
        <h2 class="section-title">كتب مستعملة</h2>
        <div class="books-grid">
            <?php foreach ($used_books as $book): 
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
    </section>
</div>
<?php include 'includes/footer.php'; ?>
