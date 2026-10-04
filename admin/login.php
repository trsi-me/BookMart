<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/asset_version.php';

if (isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'يرجى إدخال البريد وكلمة المرور';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $role = isset($user['role']) ? $user['role'] : 'user';
            if ($role !== 'admin') {
                $error = 'ليس لديك صلاحية الدخول للوحة التحكم';
            } else {
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_name'] = $user['name'];
            $_SESSION['admin_role'] = 'admin';
            header('Location: index.php');
            exit;
            }
        }
        $error = 'بيانات الدخول غير صحيحة أو ليس لديك صلاحية الأدمن';
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php require __DIR__ . '/../includes/theme-head.php'; ?>
    <title>تسجيل دخول الأدمن - BookMart</title>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css?v=<?php echo htmlspecialchars(BOOKMART_ASSET_VER, ENT_QUOTES, 'UTF-8'); ?>">
    <link rel="stylesheet" href="assets/admin.css?v=<?php echo htmlspecialchars(BOOKMART_ASSET_VER, ENT_QUOTES, 'UTF-8'); ?>">
</head>
<body class="admin-login">
    <?php $theme_toggle_class = 'theme-toggle--floating theme-toggle--compact'; require __DIR__ . '/../includes/theme-toggle.php'; ?>
    <div class="admin-login-box">
        <div class="login-logo">
            <img src="../assets/images/Logo.jpeg" alt="BookMart">
        </div>
        <h1>لوحة التحكم</h1>
        <?php if ($error): ?><div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label>البريد الإلكتروني</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label>كلمة المرور</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%">دخول</button>
        </form>
        <p style="text-align:center;margin-top:1rem"><a href="../index.php">العودة للموقع</a></p>
    </div>
    <script src="../assets/js/main.js?v=<?php echo htmlspecialchars(BOOKMART_ASSET_VER, ENT_QUOTES, 'UTF-8'); ?>"></script>
</body>
</html>
