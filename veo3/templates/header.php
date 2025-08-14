<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مولد البروموتس الإعلانية بالذكاء الاصطناعي</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="floating-elements">
        <div class="floating-element"></div>
        <div class="floating-element"></div>
        <div class="floating-element"></div>
    </div>
    
    <header class="bg-white shadow-md">
        <nav class="container mx-auto px-6 py-4 flex justify-between items-center">
            <div class="text-2xl font-bold text-gray-800">
                <a href="index.php">🎬 مولد البروموتس</a>
            </div>
            <div class="flex items-center">
                <a href="index.php" class="text-gray-600 hover:text-purple-600 mx-4">الرئيسية</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="logout.php" class="bg-purple-600 text-white px-4 py-2 rounded-md hover:bg-purple-700">تسجيل الخروج</a>
                <?php else: ?>
                    <a href="login.php" class="text-gray-600 hover:text-purple-600 mx-4">تسجيل الدخول</a>
                    <a href="register.php" class="bg-purple-600 text-white px-4 py-2 rounded-md hover:bg-purple-700">إنشاء حساب</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>

    <div class="container">