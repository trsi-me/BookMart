<?php
/**
 * صفحة السلة - BookMart
 * عرض الكتب المضافة مع إمكانية تعديل الكمية والحذف
 */
session_start();
require_once 'includes/db.php';

$page_title = 'السلة';
$body_class = '';

include 'includes/header.php';
?>
<div class="container">
    <h1>سلة التسوق</h1>

    <div id="cart-content" class="cart-items">
        <p id="cart-empty" style="display: none;">السلة فارغة. <a href="books.php">تصفح الكتب</a></p>
        <div id="cart-items-list"></div>
        <div id="cart-summary" style="display: none;">
            <p class="cart-total">المجموع: <span id="cart-total-value">0</span> ر.س</p>
        </div>
    </div>

    <div id="cart-payment-wrap" class="cart-payment-wrap" style="display: none;">
        <?php
        $pm_mode = 'cart';
        include __DIR__ . '/includes/payment_methods_block.php';
        ?>
        <p class="cart-next-hint">بعد اختيار طريقة الدفع، أكمل الطلب من الزر أدناه.</p>
        <a href="checkout.php" class="btn btn-primary btn-block" id="cart-checkout-btn">إتمام الطلب</a>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const cart = typeof getCart === 'function' ? getCart() : [];
    const container = document.getElementById('cart-items-list');
    const emptyMsg = document.getElementById('cart-empty');
    const summary = document.getElementById('cart-summary');
    const totalEl = document.getElementById('cart-total-value');
    const payWrap = document.getElementById('cart-payment-wrap');

    if (cart.length === 0) {
        emptyMsg.style.display = 'block';
    } else {
        emptyMsg.style.display = 'none';
        summary.style.display = 'block';
        if (payWrap) payWrap.style.display = 'block';
        let total = 0;
        cart.forEach(function(item) {
            const itemTotal = item.price * item.quantity;
            total += itemTotal;
            const imgHtml = item.image && (item.image.startsWith('http') || item.image.indexOf('.') > 0)
                ? '<img src="' + item.image + '" alt="" style="width:80px;height:100px;object-fit:cover;border-radius:4px">'
                : '<div class="book-placeholder" style="width:80px;height:100px;">📚</div>';
            const div = document.createElement('div');
            div.className = 'cart-item';
            div.innerHTML = imgHtml +
                '<div class="cart-item-details">' +
                '<strong>' + item.title + '</strong><br>' +
                '<span class="cart-item-price">' + item.price.toFixed(2) + ' ر.س</span>' +
                '</div>' +
                '<div class="cart-quantity">' +
                '<button type="button" class="btn btn-small" onclick="updateQuantity(' + item.id + ', ' + (item.quantity - 1) + ')">-</button>' +
                '<input type="number" value="' + item.quantity + '" min="1" onchange="updateQuantity(' + item.id + ', this.value)">' +
                '<button type="button" class="btn btn-small" onclick="updateQuantity(' + item.id + ', ' + (item.quantity + 1) + ')">+</button>' +
                '</div>' +
                '<button type="button" class="btn btn-outline btn-small" onclick="removeFromCart(' + item.id + ')">حذف</button>';
            container.appendChild(div);
        });
        totalEl.textContent = total.toFixed(2);
    }
});
</script>
<?php include 'includes/footer.php'; ?>
