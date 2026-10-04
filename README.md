# BookMart

اسم الواجهة في العناوين: BookMart. قاعدة `includes/db.php` و`database.sql`: `bookmart_db`. مجلد المشروع: `BookMart`.

## 1. ما هو المشروع

موقع PHP لعرض كتب وبيعها عبر سلة في المتصفح ثم حفظ طلب في MySQL بعد تسجيل الدخول. يوجد حساب مستخدم ولوحة إدارة للكتب. README السابق يصفه كتجارة كتب جديدة ومستعملة بتقنيات ويب مباشرة بلا إطار عمل، وهذا يطابق غياب `composer.json` وإطار في الملفات.

## 2. لماذا يوجد هذا المشروع

الصفحات تعرض كتباً من جدول `books` وتتيح إتمام طلب في `checkout.php`. لوحة `admin/` تدير الكتب وتعرض أعداد الكتب والمستخدمين والطلبات ومجموع `total_price`. سبب تجاري خارج الكود: غير موثق.

## 3. من يستخدمه

| الطرف | الآلية |
| --- | --- |
| زائر | تصفح `index.php` و`books.php` و`book.php` وسلة `localStorage` |
| مستخدم `users.role = user` | `pages/login.php` يضبط `$_SESSION['user_id']` ويستطيع `checkout.php` و`pages/profile.php` |
| مدير `users.role = admin` | `admin/login.php` يشترط الدور `admin` ويضبط `admin_id` و`admin_role` |

## 4. ماذا يستطيع النظام أن يفعل

- عرض الكتب ذات `status = published` في `books.php`.
- صفحة كتاب `book.php` وسلة `cart.php`.
- إضافة للسلة من `assets/js/main.js` بالمفتاح `bookmart_cart` في `localStorage`.
- إتمام طلب في `checkout.php` بعد الدخول: عنوان وملاحظات وطريقة دفع، ثم `INSERT` في `orders` و`order_items`.
- طرق واجهة الدفع في `includes/payment_methods_block.php`: `cod` و`apple_pay` و`bank_transfer`.
- تسجيل ودخول وملف شخصي وصفحة `pages/about.php`.
- وضع ليلي عبر `bookmart_theme` و`includes/theme-head.php` و`includes/theme-toggle.php`.
- إدارة: إحصاءات `admin/index.php`، قائمة وبحث `admin/books.php`، إضافة `admin/book-add.php`، تعديل `admin/book-edit.php`، حذف `admin/book-delete.php`.
- حالات الكتاب في النماذج: `published` و`draft` و`restricted`. الحالة في الواجهة الأمامية المعروضة: `published` فقط حسب `books.php`.

حقول `address` و`notes` و`payment_method` تُقرأ في `checkout.php`. أعمدة لها في `orders`: غير موجودة. الإدراج يحفظ `user_id` و`total_price` فقط. README السابق قال إن إتمام الطلب يحفظ الطلب بعد إدخال عنوان التوصيل. العنوان يُطلب في النموذج ولا يُكتب في `INSERT` الظاهر.

## 5. كيف يعمل النظام

```
المتصفح
  localStorage bookmart_cart
        |
        |  checkout POST cart_data (JSON)
        v
checkout.php  --جلسة user_id-->  PDO bookmart_db
        |                         orders + order_items
        v
يمسح localStorage بعد نجاح commit
```

لوحة الإدارة جلسة منفصلة المفاتيح (`admin_id`) عن جلسة المتجر (`user_id`)، وكلتاهما تقرأ جدول `users`.

## 6. أمثلة واقعية

1. زائر يضيف كتاباً من الصفحة. `addToCart(bookId, title, price, image)` يحفظ العنصر في `bookmart_cart`.
2. مستخدم مسجل يفتح `checkout.php`، يكتب `address`، يختار `cod`، ويُرسل النموذج. يُنشأ صف `orders` وصفوف `order_items` داخل `beginTransaction` / `commit`.
3. اختيار `apple_pay` بلا `apple_pay_confirmed = 1` يعيد الرسالة «يرجى إكمال خطوة Apple Pay قبل تأكيد الطلب». الزر في الواجهة معرّفه `btn-apple-pay-sim` والدالة `bindApplePaySheet()` في `main.js`. استنتاج من الكود: الخطوة واجهة داخل الصفحة وليست استدعاء Apple Pay خارجياً.
4. المدير يضيف كتاباً من `admin/book-add.php` بحالة `draft` فلا يظهر في `books.php` لأن الشرط `status = published`.

## 7. رحلة المستخدم

1. `index.php` ثم `books.php` أو `book.php`.
2. إضافة للسلة ثم `cart.php`.
3. `checkout.php` إن لم توجد جلسة يحوّل إلى `pages/login.php?redirect=checkout`.
4. إنشاء حساب من `pages/register.php` عند الحاجة.
5. تأكيد الشراء ورؤية رقم الطلب في رسالة النجاح.
6. `pages/profile.php` لتعديل البيانات.
7. المدير: `admin/login.php` ثم `admin/index.php` و`admin/books.php`.
8. خروج المتجر `logout.php` وخروج الإدارة `admin/logout.php`.

## 8. الوحدات والأقسام

| الوحدة | الملفات |
| --- | --- |
| واجهة المتجر | `index.php`, `books.php`, `book.php`, `cart.php`, `checkout.php` |
| حساب | `pages/login.php`, `pages/register.php`, `pages/profile.php`, `pages/about.php`, `logout.php` |
| مشترك | `includes/db.php`, `header.php`, `footer.php`, `theme-head.php`, `theme-toggle.php`, `payment_methods_block.php`, `asset_version.php` |
| إدارة | `admin/index.php`, `login.php`, `logout.php`, `books.php`, `book-add.php`, `book-edit.php`, `book-delete.php`, `includes/auth.php`, `includes/sidebar.php`, `assets/admin.css` |
| بيانات | `database.sql` |
| وثيقة فريق | `TEAM.md`, `README_DEFENSE.md` |

## 9. الشركات والكيانات

سجل شركة خارج الملفات: غير موثق. نص التحويل البنكي في `includes/payment_methods_block.php` يذكر اسم البنك «البنك الأهلي التجاري» والمستفيد «متجر BookMart» والعملة «ريال سعودي (SAR)». رقم الآيبان ورقم الحساب مكتوبان في الملف نفسه وغير منسوخين هنا.

## 10. الصلاحيات

| المسار | الشرط |
| --- | --- |
| عرض الكتب المنشورة والسلة | بلا دخول |
| `checkout.php` و`pages/profile.php` | `$_SESSION['user_id']` |
| `admin/*` بعد `admin/includes/auth.php` | `$_SESSION['admin_id']` وإلا تحويل إلى `admin/login.php` |
| `admin/login.php` | `password_verify` ثم `role === admin` |

مستخدم عادي يدخل `admin/login.php`: الكود يرفض غير `admin`.

## 11. الأتمتة وسير العمل

مجدول: غير موجود.

سير الطلب:

1. السلة في المتصفح.
2. `checkout.php` يفك `cart_data`.
3. المجموع من `price * quantity` كما أرسلها النموذج.
4. معاملة: `orders` ثم `order_items`.
5. عند الاستثناء `rollBack` ورسالة «حدث خطأ أثناء إتمام الطلب».
6. عند النجاح يُمسح `bookmart_cart` و`bookmart_payment_method` و`bookmart_apple_pay_ok`.

حالات طلب (`pending` وغيرها): غير موجودة في جدول `orders`.

## 12. التكامل بين الوحدات

`books.php` يقرأ `books`. السلة تحفظ `id` وبيانات العرض في المتصفح. `checkout.php` يدرج `book_id` من JSON السلة في `order_items` دون إعادة قراءة السعر من جدول `books` في المقطع المقروء. استنتاج من الكود: السعر المحفوظ هو سعر السلة في المتصفح.

`includes/header.php` يحسب `$base` بـ `../` عندما يكون `PHP_SELF` داخل `/pages/` حتى تصح مسارات الأصول. الإدارة تستخدم `BOOKMART_ASSET_VER` من `includes/asset_version.php`.

الوضع الليلي مشترك بين المتجر ولوحة الإدارة عبر `theme-toggle.php`.

## 13. المصطلحات

| المصطلح | المعنى |
| --- | --- |
| `published` / `draft` / `restricted` | قيم `books.status` |
| `new` / `used` | قيم `books.condition` |
| `bookmart_cart` | مفتاح `localStorage` للسلة |
| `bookmart_theme` | `dark` أو `light` أو اتباع `prefers-color-scheme` |
| `cod` | الدفع عند الاستلام في واجهة النموذج |
| `apple_pay` | خيار واجهة مع تأكيد `apple_pay_confirmed` |
| `bank_transfer` | خيار يعرض بيانات حساب مكتوبة في PHP |
| `BOOKMART_ASSET_VER` | القيمة `3` لمعامل `?v=` على CSS وJS، وليس إصدار منتج |

## 14. الأسئلة الشائعة

**هل السلة تبقى بعد إغلاق المتصفح؟** نعم ما دام `localStorage` للموقع لم يُمسح، لأنها ليست في قاعدة البيانات.

**هل يُحفظ عنوان التوصيل؟** النموذج يطلبه. عمود عنوان في `orders`: غير موجود، و`INSERT` لا يمرر العنوان.

**هل Apple Pay خصم حقيقي؟** ملف الواجهة يسمي الزر `btn-apple-pay-sim`. تكامل Apple في الخادم: غير موجود.

**أي الكتب تظهر للزائر؟** `status = published` في `books.php`.

**من أين صور الكتب؟** README السابق: `assets/images/books/` وأسماء `BookN.jpeg` في `database.sql`، أو `image_url`.

## 15. المعمارية

```
+---------------------------+     +----------------------+     +------------------+
| المتصفح                   |     | PHP                  |     | MySQL            |
| main.js                   | --> | index books book     | --> | bookmart_db      |
| localStorage cart/theme   |     | cart checkout        |     | users books      |
| theme-head                | <-- | pages/*  admin/*     | <-- | orders           |
+---------------------------+     | includes/db.php PDO  |     | order_items      |
                                  +----------------------+     +------------------+
```

## 16. التقنيات المستخدمة

| التقنية | الاستخدام |
| --- | --- |
| PHP | الصفحات |
| PDO MySQL `utf8mb4` | `includes/db.php` |
| HTML و CSS | `assets/css/style.css` و`admin/assets/admin.css` |
| JavaScript | `assets/js/main.js` |
| `localStorage` | السلة والسمة وطريقة الدفع |
| خط IBM Plex Sans Arabic من Google Fonts | رابط في `admin/index.php` |
| خط محلي | `assets/fonts/Tajawal/OFL.txt` |

Bootstrap وReact وLaravel: غير مستخدمة حسب README السابق وغياب ملفات الإطار. هذا يطابق الملفات الحالية.

## 17. هيكل المشروع

```
BookMart/
├── index.php
├── books.php
├── book.php
├── cart.php
├── checkout.php
├── logout.php
├── database.sql
├── TEAM.md
├── README_DEFENSE.md
├── pages/
├── admin/
├── includes/
└── assets/css style.css
    assets/js/main.js
    assets/fonts/Tajawal/OFL.txt
```

مجلد الصور `assets/images/books/` مذكور في `database.sql` وREADME السابق. ظهور ملفات jpeg في قائمة الملفات النصية الحالية: غير ظاهر.

## 18. واجهة المستخدم

`lang="ar"` و`dir="rtl"` في `admin/index.php` وصفحات المتجر التي تتبع `header.php`. زر السمة يبدل `data-theme` على `documentElement`. أيقونات السمة SVG مضمّنة في `theme-toggle.php`. favicon: غير موجود في الملفات التي فُحصت. شريط إدارة في `admin/includes/sidebar.php`.

## 19. الخادم

خادم مضمّن: غير موجود. README السابق: Apache من XAMPP والمسار `http://localhost/BookMart/`. المنفذ في الكود: غير موثق.

## 20. مسار الطلب

1. صفحة عامة تضم `includes/db.php` عند الحاجة و`includes/header.php`.
2. `books.php` يبني استعلام الكتب المنشورة مع بحث وفلترة حسب معاملات الصفحة.
3. `checkout.php` يبدأ الجلسة ويشترط `user_id`.
4. الكتابة داخل معاملة PDO.
5. فشل الاتصال في `includes/db.php` يوقف التنفيذ ويعرض رسالة PDO.
6. الإدارة تمر من `admin/includes/auth.php` قبل المحتوى.

## 21. قاعدة البيانات

`bookmart_db` بترميز `utf8mb4_unicode_ci` ومحرك InnoDB.

### users

PK `id`. `name`, `email` UNIQUE, `password`, `phone`, `role` ENUM(`user`,`admin`) افتراضي `user`, `created_at`.

### books

PK `id`. `title`, `author`, `price` DECIMAL(10,2), `condition` ENUM(`new`,`used`) افتراضي `new`, `status` ENUM(`draft`,`published`,`restricted`) افتراضي `published`, `description`, `image`, `image_url`, `created_at`.

### orders

PK `id`. `user_id` NOT NULL وFK إلى `users(id)` ON DELETE CASCADE. `total_price`. `created_at`.

### order_items

PK `id`. `order_id` FK CASCADE إلى `orders`. `book_id` FK CASCADE إلى `books`. `quantity` افتراضي 1. `price`.

بذرة المدير: الاسم «مدير الموقع»، البريد `admin@bookmart.com`، الدور `admin`، وكلمة المرور تجزئة في SQL. النص مذكور في تعليق `database.sql` وREADME السابق وغير مُعاد هنا.

`database.sql` يدرج كتباً بحالة `published` وصور `assets/images/books/Book1.jpeg` وما بعدها. قسم `ALTER TABLE` لإضافة `role` و`status` و`image_url` معلّق في الملف للترقية اليدوية.

## 22. واجهات البرمجة

REST مستقل: غير موجود.

| الطريقة | المسار | الغرض | مدخلات | صلاحية | استجابة |
| --- | --- | --- | --- | --- | --- |
| GET | `index.php` | رئيسية | | عامة | HTML |
| GET | `books.php` | كتب منشورة | بحث وفلترة في الرابط حسب الملف | عامة | HTML |
| GET | `book.php` | تفاصيل | معرف الكتاب | عامة | HTML |
| GET | `cart.php` | سلة | | عامة | HTML والسلة من المتصفح |
| POST | `checkout.php` | إنشاء طلب | `address`, `notes`, `cart_data`, `payment_method`, `apple_pay_confirmed` | `user_id` | HTML برقم الطلب أو خطأ |
| POST | `pages/login.php` | دخول متجر | `email`, `password` | | تحويل أو HTML |
| POST | `pages/register.php` | حساب | حقول التسجيل | | HTML |
| GET/POST | `pages/profile.php` | الملف | | `user_id` | HTML |
| GET | `pages/about.php` | عن الموقع | | عامة | HTML |
| GET | `logout.php` | خروج | | | تحويل |
| POST | `admin/login.php` | دخول إدارة | بريد وكلمة مرور | دور `admin` | تحويل إلى `admin/index.php` |
| GET | `admin/index.php` | أعداد | | `admin_id` | HTML |
| GET | `admin/books.php` | كتب | `status` اختياري | `admin_id` | HTML |
| POST | `admin/book-add.php` | إضافة | `title`, `author`, `price`, `condition`, `status`, `description`, `image_url` | `admin_id` | HTML |
| POST | `admin/book-edit.php` | تعديل | نفس الحقول ومعرف | `admin_id` | HTML |
| طلب الحذف | `admin/book-delete.php` | حذف كتاب | معرف | `admin_id` | تحويل أو HTML حسب الملف |

عمليات السلة `addToCart` و`removeFromCart` و`updateQuantity` تعمل في المتصفح وليست مسارات خادم.

## 23. تسجيل الدخول والصلاحيات

دخول المتجر في `pages/login.php` يتحقق من البريد و`password_verify` ويضبط جلسة المستخدم. إن وُجد `user_id` مسبقاً يحوّل إلى `index.php`.

دخول الإدارة جلسة مختلفة: `admin_id` و`admin_name` و`admin_role`. `admin/includes/auth.php` يفحص `admin_id` فقط.

CSRF: غير موجود في الملفات المقروءة. خصائص كعكة الجلسة: غير موثقة.

## 24. الحماية

- PDO بعبارات محضرة في الإضافة والتعديل والطلب.
- `password_hash` في التسجيل حسب README السابق ونمط `password_verify` في الدخول.
- `htmlspecialchars` في مخرجات الإدارة والنماذج المقروءة.
- حصر `status` في `book-add.php` و`book-edit.php` على القيم الثلاث.
- سعر الطلب يأتي من `cart_data` في المتصفح ولا يُعاد التحقق من `books.price` في مقطع `checkout.php` المقروء.
- العنوان لا يُخزن.
- رسالة اتصال القاعدة تعرض نص PDO.
- `$password` في `includes/db.php` فارغ والمستخدم `root`. ملف `.env`: غير موجود.
- بيانات حساب بنكي ثابتة داخل PHP.

## 25. الإعدادات

| الرمز | الملف | القيمة الحالية |
| --- | --- | --- |
| `$host` | `includes/db.php` | `localhost` |
| `$dbname` | `includes/db.php` | `bookmart_db` |
| `$username` | `includes/db.php` | `root` |
| `$password` | `includes/db.php` | فارغ في الملف |
| `BOOKMART_ASSET_VER` | `includes/asset_version.php` | `3` |

متغيرات بيئة: غير موجودة.

## 26. التكاملات الخارجية

| التكامل | الحالة في الملفات |
| --- | --- |
| Google Fonts | رابط CSS في `admin/index.php` |
| Apple Pay | محاكاة واجهة في `main.js`، بلا مفاتيح بوابة |
| تحويل بنكي | نص ثابت في `payment_methods_block.php` |
| بوابة دفع تنفذ خصماً | غير موجود |

## 27. المهام المجدولة

Cron: غير موجود في الملفات الحالية.

## 28. تخزين الملفات

صور الكتب مسار `image` أو رابط `image_url`. رفع ملف من نموذج الإدارة في `book-add.php`: الحقل المستخدم `image_url` نصي، وحقل رفع ثنائي في المقطع المقروء: غير موجود. السلة ليست على الخادم.

## 29. السجلات والمتابعة

نظام log: غير موجود. نجاح الطلب رسالة HTML تتضمن رقم `lastInsertId`. فشل المعاملة رسالة عامة. فشل الاتصال يطبع الاستثناء.

## 30. التثبيت

من README السابق والملفات:

1. تشغيل Apache وMySQL (XAMPP مذكور في README السابق: https://www.apachefriends.org).
2. نسخ المجلد إلى `htdocs` بحيث يصبح المسار مثل `C:\xampp\htdocs\BookMart\`.
3. إنشاء القاعدة `bookmart_db` بترميز `utf8mb4_unicode_ci` أو الاعتماد على `CREATE DATABASE` داخل `database.sql`.
4. استيراد `database.sql`.
5. تعديل `includes/db.php` إذا كانت كلمة مرور MySQL غير فارغة.
6. فتح `http://localhost/BookMart/` ولوحة `http://localhost/BookMart/admin/`.
7. إن كانت قاعدة قديمة بلا `role` أو `status` أو `image_url`: إزالة التعليق عن `ALTER TABLE` في `database.sql` وتنفيذها، كما يذكر README السابق.

## 31. دليل التطوير

- مسارات الصفحات داخل `pages/` تعتمد على `$base` في `header.php`.
- أي أصل CSS أو JS يُستدعى مع `BOOKMART_ASSET_VER`. تعليق الملف يطلب زيادة الرقم عند تغيير الأصول.
- السلة شكل JSON في `localStorage`. حقل `cart_data` يجب أن يحوي `id` و`price` و`quantity`.
- `TEAM.md` يوثق تقسيم أعضاء الفريق المذكور في README السابق: أنس الشهراني، عبدالله الدوسري، محمد قيسي، رائد قميري.
- اختبارات آلية: غير موجودة.

## 32. النشر

ملف Docker أو استضافة جاهزة: غير موجود. النشر العملي من الوثائق: خادم PHP وMySQL ونسخ الملفات واستيراد `database.sql` وضبط `includes/db.php`.

## 33. النسخ الاحتياطي والاستعادة

سكربت نسخ: غير موجود. المرجع `database.sql`. التعليق في الملف يوضح أن إعادة استيراد الكتب قد تحتاج `DELETE FROM order_items` ثم `DELETE FROM books` لأن إدراج الكتب ليس `INSERT IGNORE` لكل الصفوف. حساب المدير يستخدم `INSERT IGNORE`.

## 34. تشخيص المشكلات

| العرض | المطابق |
| --- | --- |
| خطأ اتصال PDO | MySQL أو `includes/db.php` |
| الكتب لا تظهر | `status` ليس `published` أو الاستيراد لم يتم |
| إتمام الطلب يعيد لصفحة الدخول | لا `user_id` |
| السلة فارغة عند الدفع | `cart_data` فارغ أو `localStorage` فارغ |
| Apple Pay يرفض التأكيد | `apple_pay_confirmed` ليس `1` |
| صور مكسورة | ملف `assets/images/books/` غير موجود رغم المسار في SQL |
| أعمدة ناقصة على قاعدة قديمة | تنفيذ `ALTER` المعلّق |

## 35. الاعتماديات

`composer.json` و`package.json`: غير موجودان. PHP مع PDO MySQL، MySQL، متصفح يدعم `localStorage`. خط Google Fonts يحتاج شبكة عند فتح لوحة الإدارة.

## 36. القيود المعروفة

- عنوان الطلب والملاحظات وطريقة الدفع لا تُخزن في `orders`.
- سعر السطر يأتي من العميل.
- Apple Pay محاكاة واجهة.
- CSRF غير موجود.
- favicon غير موجود.
- حالات شحن للطلب غير موجودة في المخطط.
- بيانات بنكية ثابتة في المصدر.

## 37. حالة النظام الحالية

صفحات المتجر والإدارة و`database.sql` موجودة. README السابق و`TEAM.md` و`README_DEFENSE.md` موجودة. بيئة منشورة: غير موثقة. إصدار منتج: غير موجود. `BOOKMART_ASSET_VER` يساوي `3`.

## 38. قرارات المعمارية

| القرار | الأثر |
| --- | --- |
| سلة في `localStorage` | تعمل بلا حساب وتُفقد عند مسح بيانات الموقع |
| طلب في MySQL بعد الدخول | الضيف لا ينشئ `orders` |
| جلسة إدارة بمفاتيح `admin_*` | منفصلة عن جلسة المتجر |
| حالات كتاب ثلاث | الواجهة العامة تلتزم `published` |
| دفع على مستوى الواجهة | بلا بوابة خصم في الخادم |

## 39. سجل التغييرات

سجل إصدارات: غير موجود. README السابق وصف التشغيل والجداول. هذا الملف يضيف أن `address` و`payment_method` لا يُكتبان في `INSERT`، وأن Apple Pay في الكود محاكاة.

## System Overview

BookMart is a PHP/MySQL book store. Published books are listed from `books`. The cart lives in `localStorage` under `bookmart_cart`. Checkout requires a logged-in user and writes `orders` and `order_items`. Admins with `users.role = admin` manage books. Payment choices are collected in the form and are not columns in `orders`.

## Quick Reference

| البند | القيمة |
| --- | --- |
| القاعدة | `bookmart_db` |
| الاتصال | `includes/db.php` |
| SQL | `database.sql` |
| مدير البذرة | `admin@bookmart.com` |
| سلة | `localStorage` مفتاح `bookmart_cart` |
| سمة | `bookmart_theme` |
| حالات الكتاب | `draft`, `published`, `restricted` |
| دفع الواجهة | `cod`, `apple_pay`, `bank_transfer` |
| معامل الأصول | `BOOKMART_ASSET_VER` = `3` |

## Quick Start

1. شغّل Apache وMySQL.
2. استورد `database.sql` إلى `bookmart_db`.
3. افتح `/BookMart/` حسب مسار `htdocs`.
4. لوحة الإدارة: `/BookMart/admin/`. كلمة المرور في تعليق SQL وغير مكررة هنا.

## For Non-Technical Users

تصفح الكتب المنشورة وأضفها إلى السلة بدون حساب. إتمام الشراء يطلب تسجيل الدخول وعنوان التوصيل واختيار طريقة دفع. بعد النجاح يظهر رقم الطلب. إدارة الكتب من لوحة المدير. تتبع الشحنة داخل الموقع: غير موجود في الجداول الحالية.

## For Developers

اقرأ `checkout.php` قبل افتراض أن العنوان أو طريقة الدفع مخزنة. أعد تسعير السلة من `books` إذا لزم تثبيت السعر. زِد `BOOKMART_ASSET_VER` عند تغيير CSS أو JS. الإضافة الإدارية تكتب `image_url` ولا ترفع ملفاً في `book-add.php`.
