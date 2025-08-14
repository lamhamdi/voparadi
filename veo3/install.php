<?php
require_once 'config.php';

$message = '';
$error = false;

try {
    // Check if tables already exist
    $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
    $user_table_exists = $stmt->rowCount() > 0;

    $stmt = $pdo->query("SHOW TABLES LIKE 'prompts'");
    $prompts_table_exists = $stmt->rowCount() > 0;

    if ($user_table_exists && $prompts_table_exists) {
        $message = "Database tables already exist. No action taken.";
    } else {
        $sql = file_get_contents('database.sql');
        $pdo->exec($sql);
        $message = "Database tables created successfully.";
    }
} catch (PDOException $e) {
    $message = "Error creating database tables: " . $e->getMessage();
    $error = true;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installation</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="bg-white p-8 rounded-lg shadow-md text-center">
        <h1 class="text-2xl font-bold mb-4">Installation Status</h1>
        <div class="p-4 rounded <?php echo $error ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700'; ?>">
            <?php echo $message; ?>
        </div>
        <a href="index.php" class="inline-block bg-purple-600 text-white px-6 py-2 rounded-md hover:bg-purple-700 mt-6">Go to Home</a>
    </div>
</body>
</html>