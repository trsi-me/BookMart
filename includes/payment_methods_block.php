<?php
/**
 * كتلة اختيار طريقة الدفع — تُستخدم في إتمام الطلب والسلة
 * $pm_mode: 'checkout' | 'cart'
 */
if (!isset($pm_mode)) {
    $pm_mode = 'checkout';
}
$is_cart = ($pm_mode === 'cart');
$sfx = $is_cart ? '_cart' : '';
$radio_name = $is_cart ? 'payment_method_cart' : 'payment_method';
$panel_pre = $is_cart ? 'cart-panel-' : 'panel-';
$overlay_id = $is_cart ? 'cart-apple-pay-overlay' : 'apple-pay-overlay';
$btn_id = $is_cart ? 'cart-btn-apple-pay-sim' : 'btn-apple-pay-sim';
$status_id = $is_cart ? 'cart-apple-pay-status' : 'apple-pay-status';
$amount_id = $is_cart ? 'cart-apple-pay-amount' : 'apple-pay-amount';
$auth_id = $is_cart ? 'cart-apple-pay-auth-msg' : 'apple-pay-auth-msg';
$spinner_id = $is_cart ? 'cart-apple-pay-spinner' : 'apple-pay-spinner';

$post_pm = $_POST['payment_method'] ?? 'cod';
if ($is_cart) {
    $post_pm = 'cod';
}
?>
<div class="checkout-payment-section<?php echo $is_cart ? ' cart-payment-section' : ''; ?>">
    <h2 class="checkout-payment-title">طريقة الدفع</h2>
    <?php if (!$is_cart): ?>
    <p class="payment-option-hint" style="margin-bottom:1rem">اختر الطريقة المناسبة لك قبل تأكيد الطلب.</p>
    <?php else: ?>
    <p class="payment-option-hint" style="margin-bottom:1rem">يمكنك اختيار الطريقة هنا أو في صفحة إتمام الطلب.</p>
    <?php endif; ?>

    <div class="payment-options">
        <label class="payment-option">
            <input type="radio" name="<?php echo htmlspecialchars($radio_name, ENT_QUOTES, 'UTF-8'); ?>" value="cod" <?php echo (!$is_cart && (!isset($_POST['payment_method']) || $post_pm === 'cod')) || ($is_cart) ? 'checked' : ''; ?> <?php echo $is_cart ? 'data-cart-default' : ''; ?>>
            <span class="payment-option-label">
                <strong>الدفع عند الاستلام</strong>
                <span class="payment-option-hint">الدفع نقداً عند استلام الطلب.</span>
            </span>
        </label>
        <label class="payment-option">
            <input type="radio" name="<?php echo htmlspecialchars($radio_name, ENT_QUOTES, 'UTF-8'); ?>" value="apple_pay" <?php echo !$is_cart && isset($_POST['payment_method']) && $post_pm === 'apple_pay' ? 'checked' : ''; ?>>
            <span class="payment-option-label">
                <strong>Apple Pay</strong>
                <span class="payment-option-hint">الدفع السريع عبر Apple Pay.</span>
            </span>
        </label>
        <label class="payment-option">
            <input type="radio" name="<?php echo htmlspecialchars($radio_name, ENT_QUOTES, 'UTF-8'); ?>" value="bank_transfer" <?php echo !$is_cart && isset($_POST['payment_method']) && $post_pm === 'bank_transfer' ? 'checked' : ''; ?>>
            <span class="payment-option-label">
                <strong>تحويل بنكي</strong>
                <span class="payment-option-hint">تحويل إلى حساب المتجر.</span>
            </span>
        </label>
    </div>

    <div id="<?php echo $panel_pre; ?>cod" class="payment-detail-panel" hidden>
        <p>سيتم تأكيد الطلب والدفع عند استلام الشحنة.</p>
    </div>

    <div id="<?php echo $panel_pre; ?>apple" class="payment-detail-panel" hidden>
        <p>اضغط الزر لإتمام خطوة الدفع بـ <strong>Apple Pay</strong> ثم أكّد الطلب.</p>
        <div class="apple-pay-sim">
            <button type="button" class="btn-apple-pay" id="<?php echo htmlspecialchars($btn_id, ENT_QUOTES, 'UTF-8'); ?>" aria-label="الدفع بـ Apple Pay">Pay with Apple Pay</button>
            <p id="<?php echo htmlspecialchars($status_id, ENT_QUOTES, 'UTF-8'); ?>" class="apple-pay-status" style="display:none"></p>
        </div>
    </div>

    <div id="<?php echo $panel_pre; ?>bank" class="payment-detail-panel" hidden>
        <p><strong>بيانات الحساب البنكي للمتجر</strong></p>
        <table class="bank-details-table">
            <tr><th>اسم البنك</th><td>البنك الأهلي التجاري</td></tr>
            <tr><th>اسم المستفيد</th><td>متجر BookMart</td></tr>
            <tr><th>رقم الآيبان (IBAN)</th><td dir="ltr">SA03 8000 0000 6080 1016 7519</td></tr>
            <tr><th>رقم الحساب</th><td dir="ltr">608010167519</td></tr>
            <tr><th>العملة</th><td>ريال سعودي (SAR)</td></tr>
        </table>
        <p class="payment-option-hint" style="margin-top:0.75rem">بعد تأكيد الطلب اذكر رقم الطلب في ملاحظات التحويل.</p>
    </div>
</div>

<div class="apple-pay-overlay" id="<?php echo htmlspecialchars($overlay_id, ENT_QUOTES, 'UTF-8'); ?>" aria-hidden="true">
    <div class="apple-pay-sheet" role="dialog" aria-labelledby="apple-pay-dialog-title<?php echo htmlspecialchars($sfx, ENT_QUOTES, 'UTF-8'); ?>">
        <p id="apple-pay-dialog-title<?php echo htmlspecialchars($sfx, ENT_QUOTES, 'UTF-8'); ?>" class="apple-pay-sheet-header">Apple Pay — BookMart</p>
        <div class="apple-pay-sheet-amount" id="<?php echo htmlspecialchars($amount_id, ENT_QUOTES, 'UTF-8'); ?>">0.00 <span style="font-size:0.65em">SAR</span></div>
        <div class="apple-pay-sheet-store">BookMart</div>
        <div class="apple-pay-sheet-auth" id="<?php echo htmlspecialchars($auth_id, ENT_QUOTES, 'UTF-8'); ?>">جارٍ التحقق…</div>
        <div class="apple-pay-spinner" id="<?php echo htmlspecialchars($spinner_id, ENT_QUOTES, 'UTF-8'); ?>"></div>
    </div>
</div>
