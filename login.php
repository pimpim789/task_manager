<?php
session_start();

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (($_POST['username'] ?? '') === 'admin' && ($_POST['password'] ?? '') === '12345') {
        $_SESSION['is_logged_in'] = true;
        $_SESSION['user_name'] = 'Адміністратор';
        header('Location: index.php');
        exit;
    } else {
        $error = "Невірний логін або пароль!";
    }
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вхід у систему</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        form { margin-bottom: 20px; padding: 15px; background: #f3f4f6; border-radius: 5px; width: 300px; }
        input { margin-bottom: 10px; padding: 5px; width: 100%; box-sizing: border-box; }
        .alert-danger { color: #dc2626; background: #fee2e2; padding: 10px; border-radius: 5px; margin-bottom: 15px; width: 300px; }
    </style>
</head>
<body>
    <h1>Вхід до системи</h1>

    <?php if (!empty($error)): ?>
        <div class="alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form action="login.php" method="POST">
        <label for="username">Логін:</label>
        <input type="text" id="username" name="username" required>

        <label for="password">Пароль:</label>
        <input type="password" id="password" name="password" required>

        <button type="submit">Увійти</button>
    </form>

    <p><a href="index.php">Повернутись на головну</a></p>
</body>
</html>