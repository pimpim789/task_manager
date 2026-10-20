<?php
$errors = [];
$title = "";
$description = "";
$priority = "Low";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = htmlspecialchars(trim($_POST['title'] ?? ''));
    $description = htmlspecialchars(trim($_POST['description'] ?? ''));
    $priority = $_POST['priority'] ?? 'Low';

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
    } else {
        $errors[] = "Будь ласка, завантажте зображення!";
    }

    if (empty($errors)) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $uploadFile = $uploadDir . basename($_FILES['avatar']['name']);
        
        if (move_uploaded_file($_FILES['avatar']['tmp_name'], $uploadFile)) {
            echo "<pre>";
            var_dump($_POST);
            var_dump($_FILES);
            echo "</pre>";
        } else {
            $errors[] = "Помилка при збереженні файлу на сервер!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Додати нове завдання</title>
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

        <label for="avatar">Зображення:</label>
        <input type="file" id="avatar" name="avatar" accept="image/png, image/jpeg">

        <button type="submit">Зберегти</button>
    </form>
</body>
</html>