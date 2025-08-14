<?php
require_once 'config.php';

$error = '';

if (isset($_SESSION["user_id"]) && $_SESSION["user_id"] === true) {
    header("location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $error = "يرجى إدخال اسم المستخدم وكلمة المرور.";
    } else {
        $sql = "SELECT id, username, password FROM users WHERE username = :username";
        
        if ($stmt = $pdo->prepare($sql)) {
            $stmt->bindParam(":username", $username, PDO::PARAM_STR);
            
            if ($stmt->execute()) {
                if ($stmt->rowCount() == 1) {
                    if ($row = $stmt->fetch()) {
                        $id = $row["id"];
                        $hashed_password = $row["password"];
                        if (password_verify($password, $hashed_password)) {
                            session_start();
                            
                            $_SESSION["user_id"] = $id;
                            $_SESSION["username"] = $username;
                            
                            header("location: index.php");
                        } else {
                            $error = "كلمة المرور التي أدخلتها غير صحيحة.";
                        }
                    }
                } else {
                    $error = "لم يتم العثور على حساب بهذا الاسم.";
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
    <h2 class="text-2xl font-bold text-center mb-6">تسجيل الدخول</h2>
    
    <?php if($error): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline"><?php echo $error; ?></span>
        </div>
    <?php endif; ?>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <div class="input-group mb-4">
            <label for="username">اسم المستخدم</label>
            <input type="text" name="username" id="username" class="input-field" required>
        </div>
        <div class="input-group mb-6">
            <label for="password">كلمة المرور</label>
            <input type="password" name="password" id="password" class="input-field" required>
        </div>
        <div>
            <button type="submit" class="generate-btn">تسجيل الدخول</button>
        </div>
    </form>
    <p class="text-center mt-4">ليس لديك حساب؟ <a href="register.php" class="text-purple-600 hover:underline">أنشئ حسابًا جديدًا</a></p>
</div>

<?php include 'templates/footer.php'; ?>