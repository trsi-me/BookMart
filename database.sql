-- ============================================================
-- BookMart — ملف SQL موحد
-- يشمل: إنشاء القاعدة والجداول، ترقية اختيارية لقواعد قديمة،
--       حساب الأدمن، كتب المشروع مع صور assets/images/books/BookN.jpeg
-- ============================================================

CREATE DATABASE IF NOT EXISTS bookmart_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE bookmart_db;

-- ---------- جدول المستخدمين ----------
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    role ENUM('user', 'admin') NOT NULL DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- جدول الكتب ----------
CREATE TABLE IF NOT EXISTS books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(150) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    `condition` ENUM('new', 'used') NOT NULL DEFAULT 'new',
    status ENUM('draft', 'published', 'restricted') NOT NULL DEFAULT 'published',
    description TEXT,
    image VARCHAR(255),
    image_url VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- جدول الطلبات ----------
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- جدول عناصر الطلب ----------
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    book_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- ترقية قاعدة قديمة (بدون أعمدة role / status / image_url)
-- أزل التعليق عن الأسطر التالية فقط إذا كانت قاعدتك من إصدار سابق
-- ============================================================
-- ALTER TABLE users ADD COLUMN role ENUM('user', 'admin') NOT NULL DEFAULT 'user' AFTER phone;
-- ALTER TABLE books ADD COLUMN status ENUM('draft', 'published', 'restricted') NOT NULL DEFAULT 'published' AFTER `condition`;
-- ALTER TABLE books ADD COLUMN image_url VARCHAR(500) AFTER image;

-- ============================================================
-- حساب الأدمن: admin@bookmart.com / 
-- ============================================================
INSERT IGNORE INTO users (name, email, password, role) VALUES
('مدير الموقع', 'admin@bookmart.com', '', 'admin');

-- ============================================================
-- كتب الموقع — الصور: assets/images/books/Book1.jpeg … Book8.jpeg
-- لإعادة الاستيراد من الصفر يمكنك حذف الكتب أولاً (انتبه لـ order_items):
-- DELETE FROM order_items; DELETE FROM books;
-- ============================================================

INSERT INTO books (title, author, price, `condition`, status, description, image, image_url) VALUES
('مقدمة في الحاسب والإنترنت', 'د. عبدالله بن عبدالعزيز الموسى', 55.00, 'new', 'published', 'هذا الكتاب هو دليلك الشامل لعالم الحاسب الآلي والإنترنت، معتمد رسمياً من الرخصة الدولية لقيادة الحاسب (ICDL-ECDL) وصادر بدعم من مكتب اليونسكو بالقاهرة، مما يجعله مرجعاً موثوقاً وذا قيمة أكاديمية عالية. يستهدف الكتاب جميع الناطقين باللغة العربية سواء كانوا طلاباً أو موظفين أو حتى أفراداً يرغبون في تطوير مهاراتهم التقنية.
أبرز ما يقدمه الكتاب:

تعلم نظامَي التشغيل Windows Vista وWindows 7 من الصفر حتى الاحتراف
إتقان حزمة Microsoft Office 2007 الكاملة (Word, Excel, PowerPoint)
فهم أساسيات الإنترنت والتعامل معه بأمان وكفاءة
مناسب للتحضير لاختبار ICDL المعتمد دولياً
مكتوب بلغة عربية واضحة وسهلة الفهم بعيداً عن التعقيد التقني

لمن هذا الكتاب؟ لكل من يريد بناء أساس قوي في مجال الحاسب الآلي، سواء كنت طالباً في المرحلة الثانوية أو الجامعية، أو موظفاً يسعى لتطوير كفاءته، أو ربة منزل تريد تعلم التقنية بلغتها الأم.', 'books/Book1.jpeg', NULL);

INSERT INTO books (title, author, price, `condition`, status, description, image, image_url) VALUES
('مبادئ الإحصاء والاحتمالات لطلبة العلوم الإدارية', 'د. محمود محمد سليم صالح', 60.00, 'new', 'published', 'مرجع أكاديمي متخصص من تأليف أستاذ الرياضيات في جامعة الأمير سطام بن عبدالعزيز، صُمِّم خصيصاً لطلاب كليات العلوم الإدارية والاقتصاد والأعمال الذين يحتاجون إلى فهم الإحصاء بأسلوب عملي مرتبط بتخصصهم.
أبرز ما يقدمه الكتاب:

شرح مبسط ومنهجي لمفاهيم الإحصاء الوصفي والاستدلالي
فهم التوزيعات الاحتمالية المهمة كالتوزيع الطبيعي وقاعدة 68-95-99.7
تطبيقات عملية مباشرة مرتبطة بمجال الإدارة والأعمال
أمثلة وتمارين محلولة تساعد على الاستيعاب السريع
مناسب لمتطلبات المقررات الجامعية في الإحصاء التطبيقي

لمن هذا الكتاب؟ لطلاب البكالوريوس في إدارة الأعمال والمحاسبة والاقتصاد، وكذلك لكل من يحتاج إلى فهم الإحصاء لأغراض البحث العلمي أو اتخاذ القرارات الإدارية.', 'books/Book2.jpeg', NULL);

INSERT INTO books (title, author, price, `condition`, status, description, image, image_url) VALUES
('Life - Student''s Book Beginner (الطبعة الثانية)', 'National Geographic Learning', 120.00, 'new', 'published', 'سلسلة Life من National Geographic Learning هي من أكثر سلاسل تعليم اللغة الإنجليزية شهرةً وانتشاراً على مستوى العالم، وهذا الكتاب هو مستوى المبتدئين منها. يتميز بأسلوبه البصري الاستثنائي المستوحى من صور وقصص ناشيونال جيوغرافيك الأصيلة.
أبرز ما يقدمه الكتاب:

تعلم اللغة الإنجليزية من الصفر بأسلوب ممتع ومحفز
محتوى حقيقي ومثير مستوحى من مجلة ناشيونال جيوغرافيك
تطوير المهارات الأربع: القراءة والكتابة والاستماع والتحدث
مفردات وقواعد مقدَّمة في سياقات طبيعية وواقعية
تأليف نخبة من المتخصصين: Helen Stephenson, John Hughes, Paul Dummett

لمن هذا الكتاب؟ للمبتدئين تماماً في اللغة الإنجليزية من جميع الأعمار، ولطلاب معاهد اللغة، وللمدارس التي تتبنى منهج ناشيونال جيوغرافيك.', 'books/Book3.jpeg', NULL);

INSERT INTO books (title, author, price, `condition`, status, description, image, image_url) VALUES
('Life - Student''s Book Pre-Intermediate (الطبعة الثانية)', 'National Geographic Learning', 125.00, 'new', 'published', 'الكتاب الثاني من سلسلة Life الشهيرة، يستهدف المستوى ما قبل المتوسط ويُعدّ الخطوة الطبيعية لمن أتمّ المستوى المبتدئ. يحمل الكتاب على غلافه صورة ريقاتا البندقية الشهيرة مما يعكس الطابع الثقافي والعالمي للسلسلة.
أبرز ما يقدمه الكتاب:

تطوير مهارات اللغة الإنجليزية بشكل تدريجي ومنظم
محتوى ثقافي غني يأخذ المتعلم في جولة حول العالم
قواعد نحوية موضحة بأمثلة تطبيقية من الحياة اليومية
تمارين متنوعة تشمل جميع المهارات اللغوية
تأليف: John Hughes, Helen Stephenson, Paul Dummett

لمن هذا الكتاب؟ لمتعلمي الإنجليزية الذين تجاوزوا مرحلة المبتدئين ويريدون الانتقال إلى مستوى أعلى، ومناسب لطلاب المدارس والمعاهد والجامعات.', 'books/Book4.jpeg', NULL);

INSERT INTO books (title, author, price, `condition`, status, description, image, image_url) VALUES
('Business Partner B1 - Coursebook', 'Pearson', 140.00, 'new', 'published', 'من إصدارات دار النشر العملاقة Pearson بالتعاون مع Financial Times، هذا الكتاب هو مرجع متكامل لتعلم الإنجليزية التجارية على مستوى B1 وفق الإطار الأوروبي المرجعي المشترك. يجمع بين المحتوى اللغوي الأكاديمي والتطبيق المهني الحقيقي.
أبرز ما يقدمه الكتاب:

إتقان مهارات الإنجليزية في بيئة الأعمال والشركات
محتوى مدعوم من صحيفة Financial Times العالمية المتخصصة
يشمل موارد رقمية (Digital Resources) مع كود وصول داخل الكتاب
تطوير مهارات التواصل والتفاوض والعروض التجارية بالإنجليزية
مقيَّس وفق معيار GSE (Global Scale of English)
تأليف فريق متخصص: O''Keeffe, Lansford, Wright, Frendo

لمن هذا الكتاب؟ لموظفي الشركات والمؤسسات، وطلاب كليات الأعمال والإدارة، وكل من يحتاج الإنجليزية في بيئة العمل المحترفة.', 'books/Book5.jpeg', NULL);

INSERT INTO books (title, author, price, `condition`, status, description, image, image_url) VALUES
('سلسلة Life Teacher''s Book الطبعة الثالثة (ثلاثة مستويات)', 'National Geographic Learning', 95.00, 'new', 'published', 'هذه المجموعة تضم ثلاثة كتب للمعلم من سلسلة Life الطبعة الثالثة المحدّثة، وتشمل مستويات Beginner وElementary وPre-Intermediate. تُعدّ هذه الكتب الرفيق الأساسي لكل معلم يعتمد سلسلة Life في تدريسه.
أبرز ما تقدمه هذه الكتب:

خطط دروس مفصّلة وجاهزة للتطبيق في الفصل
إجابات نموذجية لجميع تمارين كتب الطالب
اقتراحات وأنشطة إضافية لتنشيط الفصل الدراسي
إرشادات للتعامل مع مختلف مستويات الطلاب
نصائح تربوية من خبراء: Daniel Barber, Nicola Meldrum, Mike Sayer, Ceri Jones

لمن هذه الكتب؟ حصراً للمعلمين والمدرسين الذين يُدرِّسون سلسلة Life في مدارسهم ومعاهدهم، وهي أداة لا غنى عنها لتحضير الدروس بشكل احترافي.', 'books/Book6.jpeg', NULL);

INSERT INTO books (title, author, price, `condition`, status, description, image, image_url) VALUES
('Introduction to Computers (الطبعة السادسة)', 'Peter Norton', 85.00, 'new', 'published', 'أحد أشهر كتب مقدمة الحاسب الآلي في العالم، من تأليف Peter Norton الأسطورة في عالم التقنية، وصادر عن دار النشر McGraw-Hill العالمية العريقة. وصل الكتاب إلى طبعته السادسة مما يدل على مكانته العلمية الراسخة.
أبرز ما يقدمه الكتاب:

تغطية شاملة لجميع مكونات الحاسب الآلي المادية والبرمجية
فهم عميق لأنظمة التشغيل والشبكات والإنترنت
يشمل تقنيات الوسائط المتعددة (Multimedia) والبرمجة وHTML وJava
محتوى مُحدَّث يعكس آخر التطورات التقنية في وقت إصداره
أسلوب أكاديمي رصين مدعوم بأمثلة وتوضيحات وافرة

لمن هذا الكتاب؟ لطلاب تخصصات الحاسب وتقنية المعلومات في الجامعات والكليات التقنية، وللمهتمين بالحصول على خلفية علمية متينة في أساسيات الحاسب الآلي.', 'books/Book7.jpeg', NULL);

INSERT INTO books (title, author, price, `condition`, status, description, image, image_url) VALUES
('Check Your English Vocabulary for Computers and Information Technology (الطبعة الثالثة)', 'Jon Marks', 70.00, 'new', 'published', 'كتاب تدريبي متخصص لتطوير المفردات الإنجليزية في مجال الحاسب وتقنية المعلومات، من سلسلة Vocabulary Workbook المشهورة. يتميز بأسلوبه التفاعلي القائم على التمارين والاختبارات الذاتية.
أبرز ما يقدمه الكتاب:

توسيع المفردات التقنية الإنجليزية المتعلقة بالحاسب والتقنية
تمارين متنوعة ومتدرجة تضمن ترسيخ المصطلحات في الذاكرة
يغطي مجالات: الأجهزة، البرمجيات، الشبكات، الإنترنت، الأمن المعلوماتي
مناسب للدراسة الذاتية دون الحاجة لمعلم
وصل إلى طبعته الثالثة مما يؤكد شعبيته الواسعة بين المتعلمين

لمن هذا الكتاب؟ لطلاب تخصصات الحاسب وتقنية المعلومات الذين يدرسون بالإنجليزية، وللمحترفين الذين يحتاجون إتقان المصطلحات التقنية في بيئة عمل دولية.', 'books/Book8.jpeg', NULL);

-- ============================================================
-- تصحيح مسارات الصور إذا كانت مسجّلة مسبقاً بأسماء خاطئة (اختياري)
-- ============================================================
-- UPDATE books SET image = 'books/Book1.jpeg', image_url = NULL WHERE title = 'مقدمة في الحاسب والإنترنت';
-- UPDATE books SET image = 'books/Book2.jpeg', image_url = NULL WHERE title = 'مبادئ الإحصاء والاحتمالات لطلبة العلوم الإدارية';
-- UPDATE books SET image = 'books/Book3.jpeg', image_url = NULL WHERE title = 'Life - Student''s Book Beginner (الطبعة الثانية)';
-- UPDATE books SET image = 'books/Book4.jpeg', image_url = NULL WHERE title = 'Life - Student''s Book Pre-Intermediate (الطبعة الثانية)';
-- UPDATE books SET image = 'books/Book5.jpeg', image_url = NULL WHERE title = 'Business Partner B1 - Coursebook';
-- UPDATE books SET image = 'books/Book6.jpeg', image_url = NULL WHERE title = 'سلسلة Life Teacher''s Book الطبعة الثالثة (ثلاثة مستويات)';
-- UPDATE books SET image = 'books/Book7.jpeg', image_url = NULL WHERE title = 'Introduction to Computers (الطبعة السادسة)';
-- UPDATE books SET image = 'books/Book8.jpeg', image_url = NULL WHERE title = 'Check Your English Vocabulary for Computers and Information Technology (الطبعة الثالثة)';
