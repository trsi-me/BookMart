<?php
/**
 * صفحة الملف الشخصي - BookMart
 */
session_start();
require_once __DIR__ . '/../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$page_title = 'الملف الشخصي';
$body_class = '';
$error = '';
$success = '';

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($name) || empty($email)) {
        $error = 'يرجى ملء الاسم والبريد الإلكتروني';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->execute([$email, $_SESSION['user_id']]);
        if ($stmt->fetch()) {
            $error = 'البريد الإلكتروني مسجل لحساب آخر';
        } else {
            if (!empty($password)) {
                if (strlen($password) < 6) {
                    $error = 'كلمة المرور يجب أن تكون 6 أحرف على الأقل';
                } else {
                    $hashed = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, phone = ?, password = ? WHERE id = ?");
                    $stmt->execute([$name, $email, $phone ?: null, $hashed, $_SESSION['user_id']]);
                }
            } else {
                $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, phone = ? WHERE id = ?");
                $stmt->execute([$name, $email, $phone ?: null, $_SESSION['user_id']]);
            }
            if (empty($error)) {
                $_SESSION['user_name'] = $name;
                $user = array_merge($user, ['name' => $name, 'email' => $email, 'phone' => $phone]);
                $success = 'تم تحديث البيانات بنجاح';
            }
        }
    }
}

include __DIR__ . '/../includes/header.php';
?>
<div class="container">
    <div class="form-container">
        <h1 class="form-title">الملف الشخصي</h1>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="name">الاسم</label>
                <input type="text" id="name" name="name" required value="<?php echo htmlspecialchars($user['name']); ?>">
            </div>
            <div class="form-group">
                <label for="email">البريد الإلكتروني</label>
                <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($user['email']); ?>">
            </div>
            <div class="form-group">
                <label for="phone">رقم الجوال</label>
                <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="password">كلمة المرور الجديدة (اتركها فارغة للإبقاء على الحالية)</label>
                <input type="password" id="password" name="password" minlength="6">
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%;">حفظ التعديلات</button>
        </form>
    </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
