<?php
declare(strict_types=1);

session_start();
require_once __DIR__ . '/Task.php';
require_once __DIR__ . '/TaskRepository.php';

if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

$repo = new TaskRepository(__DIR__ . '/tasks.json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $title    = trim($_POST['title'] ?? '');
        $deadline = trim($_POST['deadline'] ?? '') ?: null;
        if (!empty($title)) {
            $repo->add($title, $deadline);
        }
    }

    if ($action === 'complete') {
        $repo->complete((int) ($_POST['id'] ?? 0));
    }

    if ($action === 'delete') {
        $repo->delete((int) ($_POST['id'] ?? 0));
    }

    header('Location: index.php');
    exit;
}

$tasks = $repo->findAll();
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Task Manager (OOP)</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .task-done { color: green; text-decoration: line-through; }
        .task-overdue { color: red; font-weight: bold; }
        .task-pending { color: #d97706; }
        .stats { margin-bottom: 20px; font-weight: bold; background: #f3f4f6; padding: 10px; width: fit-content; border-radius: 5px; }
    </style>
</head>
<body>
    <header>
        <h1>Менеджер завдань (ООП)</h1>
        <p>Привіт, <?= $_SESSION['user_name'] ?? 'Користувач' ?>! [<a href="logout.php">Вийти</a>]</p>
    </header>

    <main>
        <div class="stats">
            Всього: <?= count($tasks) ?> |
            Виконано: <?= $repo->countCompleted() ?> |
            Прострочено: <?= $repo->countOverdue() ?>
        </div>

        <p><a href="create.php">Додати нове завдання</a></p>

        <h2>Список завдань:</h2>
        <ul>
            <?php foreach ($tasks as $task): ?>
                <?php 
                    $statusClass = 'task-pending';
                    if ($task->isCompleted()) {
                        $statusClass = 'task-done';
                    } elseif ($task->isOverdue()) {
                        $statusClass = 'task-overdue';
                    }
                ?>
                <li class="<?= $statusClass ?>">
                    <strong><?= htmlspecialchars($task->getTitle()) ?></strong> 
                    <?php if ($task->getDeadline()): ?>
                        <span>(До: <?= $task->getDeadline() ?>)</span>
                    <?php endif; ?>
                    <span>[Статус: <?= $task->getStatusLabel() ?>]</span>

                    <?php if (!$task->isCompleted()): ?>
                        <form action="index.php" method="POST" style="display:inline;">
                            <input type="hidden" name="action" value="complete">
                            <input type="hidden" name="id" value="<?= $task->getId() ?>">
                            <button type="submit">✓</button>
                        </form>
                    <?php endif; ?>

                    <form action="index.php" method="POST" style="display:inline;">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?= $task->getId() ?>">
                        <button type="submit">✕</button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
    </main>
</body>
</html>