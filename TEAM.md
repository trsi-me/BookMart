# BookMart — تقسيم العمل بين أعضاء الفريق

هذا الملف **مستقل** عن `README.md`.  
يخصّ **من سيعرض المشروع أمام الدكتور أو اللجنة**: مسؤولية كل عضو، الملفات، مقتطفات من الكود، واقتراح لما يُقال شفهياً.

---

## الأعضاء (4)

| # | الاسم | التخصص في المشروع (مختصر) |
|---|--------|---------------------------|
| 1 | **أنس الشهراني** | قاعدة البيانات، الاتصال، المستخدمون، الجلسات، تسجيل الدخول والتسجيل |
| 2 | **عبدالله الدوسري** | الواجهة العامة، الهيدر/الفوتر، CSS، الصفحة الرئيسية، الكتب، صفحة الكتاب |
| 3 | **محمد قيسي** | السلة، إتمام الطلب، طرق الدفع، JavaScript (`main.js`) |
| 4 | **رائد قميري** | لوحة التحكم، صلاحية الأدمن، إدارة الكتب (إضافة/تعديل/حذف/بحث) |

---

## 1) أنس الشهراني

### ماذا تشرح باختصار؟
«أنا مسؤول عن **طبقة البيانات والأمان الأساسي للمستخدمين**: تصميم الجداول، الاتصال بـ MySQL عبر PDO، تشفير كلمة المرور، الجلسات، وصفحات التسجيل والدخول والملف الشخصي.»

### الملفات التي تعتمد عليها في العرض
- `database.sql` — إنشاء القاعدة والجداول والعلاقات
- `includes/db.php` — إنشاء كائن `$pdo`
- `pages/login.php`, `pages/register.php`, `pages/profile.php`
- `logout.php`

### مقتطف — اتصال آمن بقاعدة البيانات (`includes/db.php`)

```php
$pdo = new PDO(
    "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
    $username,
    $password,
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]
);
```

### مقتطف — تسجيل الدخول والتحقق من كلمة المرور (`pages/login.php`)

```php
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['name'];
    // إعادة توجيه لإتمام الطلب إن وُجد ?redirect=checkout
    header('Location: ' . $target);
    exit;
}
```

### ماذا تقول للجنة؟ (نقاط جاهزة)
1. **الجداول:** أشرح جدول `users` وربطه بـ `orders` عبر المفتاح الأجنبي.
2. **الأمان:** كلمة المرور لا تُخزن صريحة؛ نستخدم `password_hash` عند التسجيل و`password_verify` عند الدخول.
3. **الجلسة:** `$_SESSION['user_id']` يحدد المستخدم المسجّل في كل الصفحات المحمية.

---

## 2) عبدالله الدوسري

### ماذا تشرح باختصار؟
«أنا مسؤول عن **واجهة الموقع للزائر**: الهيكل المشترك، التصميم، عرض الكتب في الرئيسية وقائمة الكتب وصفحة التفاصيل، مع الانتباه لعرض البيانات بأمان.»

### الملفات
- `includes/header.php`, `includes/footer.php`
- `assets/css/style.css`
- `index.php`, `books.php`, `book.php`
- `pages/about.php`

### مقتطف — جلب كتب للصفحة الرئيسية (`index.php`)

```php
$stmt = $pdo->prepare(
    "SELECT * FROM books WHERE status = 'published' ORDER BY created_at DESC LIMIT 6"
);
$stmt->execute();
$featured_books = $stmt->fetchAll();
```

### مقتطف — تقليل هجمات XSS في العرض

```php
echo htmlspecialchars($book['title'], ENT_QUOTES, 'UTF-8');
```

### مقتطف — تصحيح المسار من مجلد `pages/` (`includes/header.php`)

```php
$base = (strpos($_SERVER['PHP_SELF'], '/pages/') !== false) ? '../' : '';
```

### ماذا تقول للجنة؟
1. **إعادة الاستخدام:** الهيدر والفوتر مرة واحدة وتُضمَّن في كل الصفحات.
2. **الأمان في العرض:** `htmlspecialchars` عند طباعة بيانات المستخدم أو قاعدة البيانات في HTML.
3. **التجربة:** شبكة الكتب وتنسيق البطاقات في `style.css` تدعم الشاشات المختلفة.

---

## 3) محمد قيسي

### ماذا تشرح باختصار؟
«أنا مسؤول عن **تجربة الشراء**: السلة في المتصفح، صفحة إتمام الطلب، حفظ الطلب في قاعدة البيانات، وواجهة اختيار طريقة الدفع مع ربط JavaScript.»

### الملفات
- `cart.php`, `checkout.php`
- `includes/payment_methods_block.php`
- `assets/js/main.js` (دوال السلة و`initCheckoutPayment` و`initCartPaymentBlock`)

### مقتطف — السلة من `localStorage` (`assets/js/main.js`)

```javascript
function getCart() {
    try {
        const cart = localStorage.getItem('bookmart_cart');
        return cart ? JSON.parse(cart) : [];
    } catch (e) {
        return [];
    }
}
```

### مقتطف — حفظ الطلب وعناصره (`checkout.php`)

```php
$pdo->beginTransaction();
$stmt = $pdo->prepare("INSERT INTO orders (user_id, total_price) VALUES (?, ?)");
$stmt->execute([$_SESSION['user_id'], $total]);
$order_id = $pdo->lastInsertId();

foreach ($cart as $item) {
    $stmt = $pdo->prepare(
        "INSERT INTO order_items (order_id, book_id, quantity, price) VALUES (?, ?, ?, ?)"
    );
    $stmt->execute([$order_id, $item['id'], $item['quantity'], $item['price']]);
}
$pdo->commit();
```

### ماذا تقول للجنة؟
1. **السلة بدون تسجيل:** تُخزَّن محلياً؛ عند الطلب يُطلب تسجيل الدخول ثم تُرسل بيانات السلة مع النموذج.
2. **الطلب:** استخدام **Transaction** حتى لا يُحفظ طلب بدون عناصر إذا حدث خطأ.
3. **الدفع في الواجهة:** المستخدم يختار الطريقة؛ لـ Apple Pay نتحقق من إكمال الخطوة في الواجهة قبل الإرسال.

---

## 4) رائد قميري

### ماذا تشرح باختصار؟
«أنا مسؤول عن **لوحة التحكم للمدير**: الدخول بصلاحية أدمن، حماية الصفحات، عرض وإدارة الكتب مع بحث وفلترة، وإضافة وتعديل وحذف.»

### الملفات
- `admin/includes/auth.php`
- `admin/login.php`, `admin/index.php`
- `admin/books.php`, `admin/book-add.php`, `admin/book-edit.php`, `admin/book-delete.php`
- `admin/assets/admin.css`

### مقتطف — منع الوصول بدون جلسة أدمن (`admin/includes/auth.php`)

```php
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}
```

### مقتطف — استعلام مع بحث آمن (`admin/books.php`)

```php
$sql = "SELECT * FROM books WHERE 1=1";
$params = [];
if ($search) {
    $sql .= " AND (title LIKE ? OR author LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
```

### ماذا تقول للجنة؟
1. **الفصل:** مجلد `admin/` منفصل عن الموقع العام.
2. **الحماية:** كل صفحة إدارة تتضمن `auth.php` للتحقق من الجلسة.
3. **الاستعلامات:** استخدام `prepare` و`?` لتقليل خطر حقن SQL عند البحث.

---

## ختام — عرض جماعي مقترح (دقيقة واحدة)

- **أنس:** قاعدة البيانات + تسجيل المستخدمين بأمان.  
- **عبدالله:** شكل الموقع وعرض الكتب للزائر.  
- **محمد:** السلة وإتمام الطلب وحفظ الطلبات.  
- **رائد:** إدارة المحتوى من لوحة التحكم.

---

*هذا الملف لا يغني عن قراءة `README.md` للتشغيل والهيكل التقني العام.*
