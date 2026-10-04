<?php
/**
 * صفحة تسجيل الدخول - BookMart
 */
session_start();
require_once __DIR__ . '/../includes/db.php';

// إعادة التوجيه إذا كان المستخدم مسجلاً
if (isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}

$page_title = 'تسجيل الدخول';
$body_class = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'يرجى إدخال البريد الإلكتروني وكلمة المرور';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $redirect = $_GET['redirect'] ?? '';
            $target = ($redirect === 'checkout') ? '../checkout.php' : '../index.php';
            header('Location: ' . $target);
            exit;
        } else {
            $error = 'البريد الإلكتروني أو كلمة المرور غير صحيحة';
        }
    }
}

include __DIR__ . '/../includes/header.php';
?>
<div class="container">
    <div class="form-container">
        <div class="login-logo">
            <img src="../assets/images/Logo.jpeg" alt="BookMart">
        </div>
        <h1 class="form-title">تسجيل الدخول</h1>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="email">البريد الإلكتروني</label>
                <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="password">كلمة المرور</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%;">تسجيل الدخول</button>
        </form>
        <p style="text-align: center; margin-top: 1rem;">
            ليس لديك حساب؟ <a href="register.php">إنشاء حساب جديد</a>
        </p>
    </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
