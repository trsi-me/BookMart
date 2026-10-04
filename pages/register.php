<?php
/**
 * صفحة إنشاء حساب جديد - BookMart
 */
session_start();
require_once __DIR__ . '/../includes/db.php';

if (isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}

$page_title = 'إنشاء حساب';
$body_class = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    if (empty($name) || empty($email) || empty($password)) {
        $error = 'يرجى ملء جميع الحقول المطلوبة (*)';
    } elseif (strlen($name) < 3) {
        $error = 'الاسم يجب أن يكون 3 أحرف على الأقل';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'البريد الإلكتروني غير صحيح';
    } elseif (strlen($password) < 8) {
        $error = 'كلمة المرور يجب أن تكون 8 أحرف على الأقل';
    } elseif (!preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password)) {
        $error = 'كلمة المرور يجب أن تحتوي على حروف وأرقام';
    } elseif ($password !== $password_confirm) {
        $error = 'كلمتا المرور غير متطابقتين';
    } elseif ($phone && !preg_match('/^[0-9\s\-\+]{10,15}$/', $phone)) {
        $error = 'رقم الجوال غير صحيح';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = 'البريد الإلكتروني مسجل مسبقاً';
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, phone) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $email, $hashed, $phone ?: null]);
            $_SESSION['user_id'] = $pdo->lastInsertId();
            $_SESSION['user_name'] = $name;
            header('Location: ../index.php');
            exit;
        }
    }
}

include __DIR__ . '/../includes/header.php';
?>
<div class="container">
    <div class="form-container form-card">
        <div class="login-logo">
            <img src="../assets/images/Logo.jpeg" alt="BookMart">
        </div>
        <h1 class="form-title">إنشاء حساب جديد</h1>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="" autocomplete="on">
            <div class="form-group">
                <label for="name">الاسم الكامل <span class="required">*</span></label>
                <input type="text" id="name" name="name" required minlength="3" 
                    placeholder="أدخل اسمك الكامل" 
                    value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>"
                    autocomplete="name">
            </div>
            <div class="form-group">
                <label for="email">البريد الإلكتروني <span class="required">*</span></label>
                <input type="email" id="email" name="email" required 
                    placeholder="example@email.com" 
                    value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                    autocomplete="email">
            </div>
            <div class="form-group">
                <label for="phone">رقم الجوال</label>
                <input type="tel" id="phone" name="phone" 
                    placeholder="05xxxxxxxx" 
                    value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>"
                    autocomplete="tel">
            </div>
            <div class="form-group">
                <label for="password">كلمة المرور <span class="required">*</span></label>
                <input type="password" id="password" name="password" required minlength="8"
                    placeholder="8 أحرف على الأقل (حروف وأرقام)"
                    autocomplete="new-password">
                <small class="form-hint">يُفضّل أن تحتوي على حروف وأرقام</small>
            </div>
            <div class="form-group">
                <label for="password_confirm">تأكيد كلمة المرور <span class="required">*</span></label>
                <input type="password" id="password_confirm" name="password_confirm" required minlength="8"
                    placeholder="أعد إدخال كلمة المرور"
                    autocomplete="new-password">
            </div>
            <button type="submit" class="btn btn-primary btn-block">إنشاء الحساب</button>
        </form>
        <p class="form-footer-link">
            لديك حساب؟ <a href="login.php">تسجيل الدخول</a>
        </p>
    </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
