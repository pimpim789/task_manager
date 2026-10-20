<?php
  $appName = "Task Manager";
  $taskTitle = "Вивчити основи PHP та навчитися писати власні функції для обробки рядків";
  $taskTimeEstimate = 2; 
  $isCompleted = false;

  function formatTitle($text, $maxLength = 20) {
      if (mb_strlen($text, 'UTF-8') > $maxLength) {
          return mb_substr($text, 0, $maxLength, 'UTF-8') . "...";
      }
      return $text;
  }

  function getCurrentGreeting() {
      $hour = (int)date('H'); 
      if ($hour >= 6 && $hour <= 11) {
          return "Доброго ранку";
      } elseif ($hour >= 12 && $hour <= 17) {
          return "Добрий день";
      } elseif ($hour >= 18 && $hour <= 23) {
          return "Добрий вечір";
      } else {
          return "Доброї ночі";
      }
  }
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
        .greeting {
            color: #4b5563;
            font-style: italic;
        }
    </style>
</head>
<body>
    <header>
        <h1><?= $appName ?></h1>
        <p class="greeting"><?= getCurrentGreeting() ?>!</p>
    </header>

    <main>
        <h2>Поточне завдання:</h2>
        <ul>
            <li class="<?= $isCompleted ? 'task-done' : 'task-pending' ?>">
                <strong>Назва:</strong> <?= formatTitle($taskTitle) ?> | 
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