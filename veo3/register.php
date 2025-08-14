<?php
require_once 'config.php';

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $password_confirm = trim($_POST['password_confirm']);

    if (empty($username) || empty($email) || empty($password) || empty($password_confirm)) {
        $error = "يرجى ملء جميع الحقول.";
    } elseif ($password !== $password_confirm) {
        $error = "كلمتا المرور غير متطابقتين.";
    } elseif (strlen($password) < 6) {
        $error = "يجب أن تكون كلمة المرور 6 أحرف على الأقل.";
    } else {
        $sql = "SELECT id FROM users WHERE username = :username OR email = :email";
        if ($stmt = $pdo->prepare($sql)) {
            $stmt->bindParam(":username", $username, PDO::PARAM_STR);
            $stmt->bindParam(":email", $email, PDO::PARAM_STR);
            if ($stmt->execute()) {
                if ($stmt->rowCount() == 1) {
                    $error = "اسم المستخدم أو البريد الإلكتروني موجود بالفعل.";
                } else {
                    $sql = "INSERT INTO users (username, email, password) VALUES (:username, :email, :password)";
                    if ($stmt = $pdo->prepare($sql)) {
                        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                        $stmt->bindParam(":username", $username, PDO::PARAM_STR);
                        $stmt->bindParam(":email", $email, PDO::PARAM_STR);
                        $stmt->bindParam(":password", $hashed_password, PDO::PARAM_STR);
                        if ($stmt->execute()) {
                            $success = "تم إنشاء حسابك بنجاح. يمكنك الآن تسجيل الدخول.";
                        } else {
                            $error = "حدث خطأ ما. يرجى المحاولة مرة أخرى في وقت لاحق.";
                        }
                    }
                }
            } else {
                $error = "حدث خطأ ما. يرجى المحاولة مرة أخرى في وقت لاحق.";
            }
        }
        unset($stmt);
    }
    unset($pdo);
}

include 'templates/header.php';
?>

<div class="main-card" style="max-width: 500px; margin: 40px auto;">
    <h2 class="text-2xl font-bold text-center mb-6">إنشاء حساب جديد</h2>
    
    <?php if($error): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline"><?php echo $error; ?></span>
        </div>
    <?php endif; ?>
    
    <?php if($success): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline"><?php echo $success; ?></span>
        </div>
    <?php endif; ?>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <div class="input-group mb-4">
            <label for="username">اسم المستخدم</label>
            <input type="text" name="username" id="username" class="input-field" required>
        </div>
        <div class="input-group mb-4">
            <label for="email">البريد الإلكتروني</label>
            <input type="email" name="email" id="email" class="input-field" required>
        </div>
        <div class="input-group mb-4">
            <label for="password">كلمة المرور</label>
            <input type="password" name="password" id="password" class="input-field" required>
        </div>
        <div class="input-group mb-6">
            <label for="password_confirm">تأكيد كلمة المرور</label>
            <input type="password" name="password_confirm" id="password_confirm" class="input-field" required>
        </div>
        <div>
            <button type="submit" class="generate-btn">إنشاء الحساب</button>
        </div>
    </form>
    <p class="text-center mt-4">لديك حساب بالفعل؟ <a href="login.php" class="text-purple-600 hover:underline">سجل الدخول</a></p>
</div>

<?php include 'templates/footer.php'; ?>