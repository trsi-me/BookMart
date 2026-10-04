<?php
/**
 * صفحة نبذة عن الموقع - BookMart
 */
session_start();
require_once __DIR__ . '/../includes/db.php';

$page_title = 'نبذة عن الموقع';
$body_class = '';

include __DIR__ . '/../includes/header.php';
?>
<div class="container">
    <div class="about-content">
        <h1>نبذة عن BookMart</h1>
        <p>BookMart هو موقع إلكتروني بسيط واحترافي لبيع الكتب الجديدة والمستعملة بطريقة سهلة تشبه المتاجر الإلكترونية المعروفة.</p>
        <p>يستطيع المستخدم تصفح الكتب ورؤية صورها ومعرفة تفاصيلها وطلبها بسهولة من خلال واجهة واضحة وسريعة.</p>
        <p>نسعى لتوفير تجربة تسوق مريحة لجميع محبي القراءة، مع إتاحة خيارات متنوعة من الكتب بأسعار مناسبة.</p>
    </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
