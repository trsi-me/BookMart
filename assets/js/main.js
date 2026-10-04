/**
 * BookMart - السكريبت الرئيسي
 * يتعامل مع السلة والنماذج
 */

document.addEventListener('DOMContentLoaded', function() {
    initTheme();

    // تهيئة السلة من التخزين المحلي
    initCart();

    // معالجة النماذج
    initForms();

    initCheckoutPayment();
    initCartPaymentBlock();
});

/**
 * الوضع الليلي — يُحفظ في localStorage ويُطبَّق على documentElement
 */
function initTheme() {
    var root = document.documentElement;

    function applyTheme(mode) {
        var next = mode === 'dark' ? 'dark' : 'light';
        root.setAttribute('data-theme', next);
        try {
            localStorage.setItem('bookmart_theme', next);
        } catch (e) {}
        syncThemeUi(next);
    }

    function syncThemeUi(mode) {
        var isDark = mode === 'dark';
        document.querySelectorAll('.theme-toggle').forEach(function(btn) {
            btn.setAttribute('aria-pressed', isDark ? 'true' : 'false');
            btn.setAttribute('aria-label', isDark ? 'التبديل إلى الوضع النهاري' : 'التبديل إلى الوضع الليلي');
            btn.setAttribute('title', isDark ? 'الوضع النهاري' : 'الوضع الليلي');
        });
    }

    syncThemeUi(root.getAttribute('data-theme') === 'dark' ? 'dark' : 'light');

    document.querySelectorAll('.theme-toggle').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var cur = root.getAttribute('data-theme') === 'dark' ? 'dark' : 'light';
            applyTheme(cur === 'dark' ? 'light' : 'dark');
        });
    });
}

/**
 * تهيئة السلة - تحميل العناصر من localStorage
 */
function initCart() {
    if (!window.getCart) return;
    const cart = getCart();
    updateCartCount(cart.length);
}

/**
 * الحصول على السلة من التخزين المحلي
 */
function getCart() {
    try {
        const cart = localStorage.getItem('bookmart_cart');
        return cart ? JSON.parse(cart) : [];
    } catch (e) {
        return [];
    }
}

/**
 * حفظ السلة في التخزين المحلي
 */
function saveCart(cart) {
    localStorage.setItem('bookmart_cart', JSON.stringify(cart));
    updateCartCount(cart.length);
}

/**
 * تحديث عداد السلة في الواجهة
 */
function updateCartCount(count) {
    const badge = document.querySelector('.cart-count') || document.getElementById('cart-badge');
    if (badge) {
        badge.textContent = count;
        badge.style.display = count > 0 ? 'flex' : 'none';
    }
}

/**
 * إضافة كتاب للسلة
 */
function addToCart(bookId, title, price, image) {
    const cart = getCart();
    const existing = cart.find(item => item.id == bookId);

    if (existing) {
        existing.quantity += 1;
    } else {
        cart.push({
            id: bookId,
            title: title,
            price: parseFloat(price),
            image: image || '',
            quantity: 1
        });
    }

    saveCart(cart);
    showToast('تمت إضافة الكتاب إلى السلة', 'success');
}

/**
 * إزالة كتاب من السلة
 */
function removeFromCart(bookId) {
    let cart = getCart();
    cart = cart.filter(item => item.id != bookId);
    saveCart(cart);
    location.reload();
}

/**
 * تحديث كمية الكتاب في السلة
 */
function updateQuantity(bookId, quantity) {
    const qty = parseInt(quantity);
    const cart = getCart();
    const item = cart.find(i => i.id == bookId);
    if (!item) return;

    if (qty < 1) {
        removeFromCart(bookId);
        return;
    }
    item.quantity = qty;
    saveCart(cart);
    location.reload();
}

/**
 * تهيئة النماذج والتحقق
 */
function initForms() {
    const forms = document.querySelectorAll('form[data-validate]');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!validateForm(form)) {
                e.preventDefault();
            }
        });
    });
}

/**
 * عرض إشعار Toast
 */
function showToast(message, type) {
    type = type || 'success';
    const container = document.getElementById('toast-container');
    if (!container) return;
    const toast = document.createElement('div');
    toast.className = 'toast ' + type;
    toast.textContent = message;
    container.appendChild(toast);
    setTimeout(function() {
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(20px)';
        setTimeout(function() { toast.remove(); }, 350);
    }, 2500);
}

/**
 * التحقق من صحة النموذج
 */
function validateForm(form) {
    let valid = true;
    const required = form.querySelectorAll('[required]');

    required.forEach(field => {
        if (!field.value.trim()) {
            valid = false;
            field.style.borderColor = '#e74c3c';
        } else {
            field.style.borderColor = '';
        }
    });

    return valid;
}

/**
 * إتمام الطلب — طرق الدفع و Apple Pay
 */
function initCheckoutPayment() {
    const form = document.getElementById('checkout-form');
    if (!form) return;

    const cartInput = document.getElementById('cart_data');
    const appleConfirmed = document.getElementById('apple_pay_confirmed');
    const panels = { cod: 'panel-cod', apple_pay: 'panel-apple', bank_transfer: 'panel-bank' };

    function syncPanelsCheckout() {
        const sel = document.querySelector('input[name="payment_method"]:checked');
        const v = sel ? sel.value : 'cod';
        Object.keys(panels).forEach(function(k) {
            const el = document.getElementById(panels[k]);
            if (el) el.hidden = (k !== v);
        });
        if (v !== 'apple_pay') {
            if (appleConfirmed) appleConfirmed.value = '0';
            localStorage.removeItem('bookmart_apple_pay_ok');
            const statusEl = document.getElementById('apple-pay-status');
            if (statusEl) {
                statusEl.style.display = 'none';
                statusEl.textContent = '';
                statusEl.classList.remove('error');
            }
        }
    }

    document.querySelectorAll('input[name="payment_method"]').forEach(function(r) {
        r.addEventListener('change', function() {
            localStorage.setItem('bookmart_payment_method', r.value);
            syncPanelsCheckout();
        });
    });

    const saved = localStorage.getItem('bookmart_payment_method');
    if (saved && ['cod', 'apple_pay', 'bank_transfer'].indexOf(saved) >= 0) {
        const inp = document.querySelector('input[name="payment_method"][value="' + saved + '"]');
        if (inp) inp.checked = true;
    }
    if (localStorage.getItem('bookmart_apple_pay_ok') === '1' && appleConfirmed) {
        appleConfirmed.value = '1';
    }
    syncPanelsCheckout();

    bindApplePaySheet({
        btnId: 'btn-apple-pay-sim',
        overlayId: 'apple-pay-overlay',
        amountId: 'apple-pay-amount',
        authId: 'apple-pay-auth-msg',
        spinnerId: 'apple-pay-spinner',
        statusId: 'apple-pay-status',
        mode: 'checkout',
        appleConfirmed: appleConfirmed
    });

    form.addEventListener('submit', function(e) {
        const cart = typeof getCart === 'function' ? getCart() : [];
        cartInput.value = JSON.stringify(cart);
        const pm = document.querySelector('input[name="payment_method"]:checked');
        if (pm && pm.value === 'apple_pay' && appleConfirmed && appleConfirmed.value !== '1') {
            e.preventDefault();
            if (typeof showToast === 'function') showToast('يرجى إكمال خطوة Apple Pay أولاً', 'error');
            else alert('يرجى إكمال خطوة Apple Pay أولاً');
        }
    });
}

/**
 * السلة — نفس خيارات الدفع قبل إتمام الطلب
 */
function initCartPaymentBlock() {
    const wrap = document.getElementById('cart-payment-wrap');
    if (!wrap) return;

    const panels = { cod: 'cart-panel-cod', apple_pay: 'cart-panel-apple', bank_transfer: 'cart-panel-bank' };

    function syncPanelsCart() {
        const sel = document.querySelector('input[name="payment_method_cart"]:checked');
        const v = sel ? sel.value : 'cod';
        Object.keys(panels).forEach(function(k) {
            const el = document.getElementById(panels[k]);
            if (el) el.hidden = (k !== v);
        });
        if (v !== 'apple_pay') {
            localStorage.removeItem('bookmart_apple_pay_ok');
            const statusEl = document.getElementById('cart-apple-pay-status');
            if (statusEl) {
                statusEl.style.display = 'none';
                statusEl.textContent = '';
                statusEl.classList.remove('error');
            }
        }
    }

    document.querySelectorAll('input[name="payment_method_cart"]').forEach(function(r) {
        r.addEventListener('change', function() {
            localStorage.setItem('bookmart_payment_method', r.value);
            syncPanelsCart();
        });
    });

    const saved = localStorage.getItem('bookmart_payment_method');
    if (saved && ['cod', 'apple_pay', 'bank_transfer'].indexOf(saved) >= 0) {
        const inp = document.querySelector('input[name="payment_method_cart"][value="' + saved + '"]');
        if (inp) inp.checked = true;
    }
    syncPanelsCart();

    bindApplePaySheet({
        btnId: 'cart-btn-apple-pay-sim',
        overlayId: 'cart-apple-pay-overlay',
        amountId: 'cart-apple-pay-amount',
        authId: 'cart-apple-pay-auth-msg',
        spinnerId: 'cart-apple-pay-spinner',
        statusId: 'cart-apple-pay-status',
        mode: 'cart',
        appleConfirmed: null
    });

    const checkoutBtn = document.getElementById('cart-checkout-btn');
    if (checkoutBtn) {
        checkoutBtn.addEventListener('click', function(e) {
            const sel = document.querySelector('input[name="payment_method_cart"]:checked');
            if (!sel) return;
            localStorage.setItem('bookmart_payment_method', sel.value);
            if (sel.value === 'apple_pay' && localStorage.getItem('bookmart_apple_pay_ok') !== '1') {
                e.preventDefault();
                if (typeof showToast === 'function') showToast('يرجى إكمال خطوة Apple Pay أولاً', 'error');
                else alert('يرجى إكمال خطوة Apple Pay أولاً');
            }
        });
    }
}

function cartTotalAmount() {
    try {
        const c = typeof getCart === 'function' ? getCart() : [];
        return c.reduce(function(s, i) {
            return s + parseFloat(i.price) * parseInt(i.quantity, 10);
        }, 0);
    } catch (e) {
        return 0;
    }
}

function bindApplePaySheet(opts) {
    const btn = document.getElementById(opts.btnId);
    if (!btn) return;

    const overlay = document.getElementById(opts.overlayId);
    const amountEl = document.getElementById(opts.amountId);
    const authMsg = document.getElementById(opts.authId);
    const spinner = document.getElementById(opts.spinnerId);
    const statusEl = document.getElementById(opts.statusId);
    const appleConfirmed = opts.appleConfirmed;

    btn.addEventListener('click', function() {
        const total = cartTotalAmount();
        if (total <= 0) {
            if (typeof showToast === 'function') showToast('السلة فارغة أو المبلغ غير صالح', 'error');
            return;
        }
        if (amountEl) {
            amountEl.innerHTML = total.toFixed(2) + ' <span style="font-size:0.65em">SAR</span>';
        }
        if (authMsg) authMsg.textContent = 'جارٍ التحقق عبر Face ID…';
        if (spinner) spinner.style.display = 'block';
        if (overlay) {
            overlay.classList.add('is-open');
            overlay.setAttribute('aria-hidden', 'false');
        }
        if (appleConfirmed) appleConfirmed.value = '0';
        if (opts.mode === 'cart') localStorage.removeItem('bookmart_apple_pay_ok');

        setTimeout(function() {
            if (authMsg) authMsg.textContent = 'تم التحقق — جارٍ إتمام الدفع…';
        }, 1200);

        setTimeout(function() {
            if (spinner) spinner.style.display = 'none';
            if (authMsg) authMsg.textContent = 'تمت عملية الدفع بنجاح';
            if (overlay) {
                overlay.classList.remove('is-open');
                overlay.setAttribute('aria-hidden', 'true');
            }
            if (appleConfirmed) appleConfirmed.value = '1';
            if (opts.mode === 'cart') localStorage.setItem('bookmart_apple_pay_ok', '1');
            if (statusEl) {
                statusEl.style.display = 'block';
                statusEl.textContent = opts.mode === 'checkout'
                    ? 'تمت خطوة Apple Pay — يمكنك تأكيد الطلب.'
                    : 'تمت خطوة Apple Pay — يمكنك المتابعة إلى إتمام الطلب.';
                statusEl.classList.remove('error');
            }
            if (typeof showToast === 'function') showToast('تمت خطوة Apple Pay', 'success');
        }, 2600);
    });
}
