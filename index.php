<?php
  $appName = "Task Manager";
  $taskTitle = "Вивчити основи PHP";
  $taskTimeEstimate = 2; 
  $isCompleted = false;  
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $appName ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
        }
        .task-done {
            color: green;
            text-decoration: line-through;
        }
        .task-pending {
            color: #d97706; 
        }
    </style>
</head>
<body>
    <header>
        <h1><?= $appName ?></h1>
    </header>

    <main>
        <h2>Поточне завдання:</h2>
        <ul>
            <li class="<?= $isCompleted ? 'task-done' : 'task-pending' ?>">
                <strong>Назва:</strong> <?= $taskTitle ?> | 
                <span>
                    <?php if ($isCompleted): ?>
                         Виконано
                    <?php else: ?>
                         В процесі
                    <?php endif; ?>
                </span>
            </li>
            <li><strong>Орієнтовний час:</strong> <?= $taskTimeEstimate ?> год.</li>
        </ul>
    </main>
</body>
</html>