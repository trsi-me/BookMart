<?php
/**
 * صفحة إتمام الطلب - BookMart
 */
session_start();
require_once 'includes/db.php';

$page_title = 'إتمام الطلب';
$body_class = 'checkout-page';
$error = '';
$success = '';

$payment_labels = [
    'cod' => 'الدفع عند الاستلام',
    'apple_pay' => 'Apple Pay',
    'bank_transfer' => 'تحويل بنكي',
];

// التحقق من تسجيل الدخول
if (!isset($_SESSION['user_id'])) {
    header('Location: pages/login.php?redirect=checkout');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $address = trim($_POST['address'] ?? '');
    $notes = trim($_POST['notes'] ?? '');
    $cart_data = $_POST['cart_data'] ?? '';
    $payment_method = $_POST['payment_method'] ?? 'cod';
    if (!isset($payment_labels[$payment_method])) {
        $payment_method = 'cod';
    }
    $apple_confirmed = ($_POST['apple_pay_confirmed'] ?? '') === '1';

    if ($payment_method === 'apple_pay' && !$apple_confirmed) {
        $error = 'يرجى إكمال خطوة Apple Pay قبل تأكيد الطلب';
    } elseif (empty($address)) {
        $error = 'يرجى إدخال عنوان التوصيل';
    } elseif (empty($cart_data)) {
        $error = 'السلة فارغة';
    } else {
        $cart = json_decode($cart_data, true);
        if (!is_array($cart) || empty($cart)) {
            $error = 'السلة فارغة';
        } else {
            $total = 0;
            foreach ($cart as $item) {
                $total += floatval($item['price']) * intval($item['quantity']);
            }

            try {
                $pdo->beginTransaction();
                $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_price) VALUES (?, ?)");
                $stmt->execute([$_SESSION['user_id'], $total]);
                $order_id = $pdo->lastInsertId();

                foreach ($cart as $item) {
                    $stmt = $pdo->prepare("INSERT INTO order_items (order_id, book_id, quantity, price) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$order_id, $item['id'], $item['quantity'], $item['price']]);
                }

                $pdo->commit();
                $pay_note = $payment_labels[$payment_method];
                if ($payment_method === 'bank_transfer') {
                    $pay_note .= ' — يرجى التحويل إلى حساب المتجر المعروض وذكر رقم الطلب: ' . $order_id;
                } elseif ($payment_method === 'cod') {
                    $pay_note .= ' — الدفع عند استلام الشحنة.';
                } else {
                    $pay_note .= ' — تم تأكيد الطلب.';
                }
                $success = 'تم إتمام الطلب بنجاح. رقم الطلب: ' . $order_id . ' — طريقة الدفع: ' . $pay_note;
                echo '<script>localStorage.removeItem("bookmart_cart");localStorage.removeItem("bookmart_payment_method");localStorage.removeItem("bookmart_apple_pay_ok");</script>';
            } catch (Exception $e) {
                $pdo->rollBack();
                $error = 'حدث خطأ أثناء إتمام الطلب';
            }
        }
    }
}

include 'includes/header.php';
?>
<div class="container">
    <h1>إتمام الطلب</h1>

    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <a href="index.php" class="btn btn-primary">العودة للرئيسية</a>
    <?php else: ?>
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="checkout-form">
            <form method="POST" action="" id="checkout-form" novalidate>
                <input type="hidden" name="cart_data" id="cart_data">
                <input type="hidden" name="apple_pay_confirmed" id="apple_pay_confirmed" value="0">

                <div class="form-group">
                    <label for="address">عنوان التوصيل</label>
                    <textarea id="address" name="address" rows="3" required><?php echo htmlspecialchars($_POST['address'] ?? ''); ?></textarea>
                </div>
                <div class="form-group">
                    <label for="notes">ملاحظات (اختياري)</label>
                    <textarea id="notes" name="notes" rows="2"><?php echo htmlspecialchars($_POST['notes'] ?? ''); ?></textarea>
                </div>

                <?php
                $pm_mode = 'checkout';
                include __DIR__ . '/includes/payment_methods_block.php';
                ?>

                <button type="submit" class="btn btn-primary" id="checkout-submit">تأكيد الشراء</button>
            </form>
        </div>
    <?php endif; ?>
</div>
<?php include 'includes/footer.php'; ?>
