<?php
session_start();

require_once __DIR__ . DIRECTORY_SEPARATOR . 'Task.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'TaskRepository.php';

if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

$repo = new TaskRepository(__DIR__ . '/tasks.json');

$errors = [];
$title = "";
$description = "";
$priority = "Low";
$deadline = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = htmlspecialchars(trim($_POST['title'] ?? ''));
    $description = htmlspecialchars(trim($_POST['description'] ?? ''));
    $priority = $_POST['priority'] ?? 'Low';
    $deadline = trim($_POST['deadline'] ?? '') ?: null; 

    if (empty($title)) {
        $errors[] = "Поле Назва є обов'язковим для заповнення!";
    }

    if (empty($description)) {
        $errors[] = "Поле Опис є обов'язковим для заповнення!";
    }

    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $fileSize = $_FILES['avatar']['size'];
        $maxSize = 2 * 1024 * 1024;

        if ($fileSize > $maxSize) {
            $errors[] = "Файл завеликий! Максимальний розмір 2 МБ.";
        }

        $fileExtension = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png'];

        if (!in_array($fileExtension, $allowedExtensions)) {
            $errors[] = "Дозволені лише формати .jpg, .jpeg та .png!";
        }
    }

    if (empty($errors)) {
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            move_uploaded_file($_FILES['avatar']['tmp_name'], $uploadDir . basename($_FILES['avatar']['name']));
        }

        $repo->add($title, $deadline);

        header('Location: index.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Створити завдання</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        form { margin-bottom: 20px; padding: 15px; background: #f3f4f6; border-radius: 5px; width: 350px; }
        input, textarea, select { margin-bottom: 10px; padding: 5px; width: 100%; box-sizing: border-box; }
        .alert-danger { color: #dc2626; background: #fee2e2; padding: 10px; border-radius: 5px; margin-bottom: 15px; width: 350px; }
    </style>
</head>
<body>
    <h1>Створити завдання</h1>
    
    <p><a href="index.php">Повернутись до списку</a></p>

    <?php if (!empty($errors)): ?>
        <div class="alert-danger">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= $error ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="create.php" method="POST" enctype="multipart/form-data">
        <label for="title">Назва завдання:</label>
        <input type="text" id="title" name="title" value="<?= $title ?>">

        <label for="description">Опис завдання:</label>
        <textarea id="description" name="description" rows="4"><?= $description ?></textarea>

        <label for="priority">Пріоритет:</label>
        <select id="priority" name="priority">
            <option value="Low" <?= $priority === 'Low' ? 'selected' : '' ?>>Low</option>
            <option value="Medium" <?= $priority === 'Medium' ? 'selected' : '' ?>>Medium</option>
            <option value="High" <?= $priority === 'High' ? 'selected' : '' ?>>High</option>
        </select>

        <label for="deadline">Дедлайн:</label>
        <input type="date" id="deadline" name="deadline" value="<?= $deadline ?>">

        <label for="avatar">Зображення:</label>
        <input type="file" id="avatar" name="avatar" accept="image/png, image/jpeg">

        <button type="submit">Зберегти</button>
    </form>
</body>
</html>