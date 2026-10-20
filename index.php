<?php
  $appName = "Task Manager";
  
  $tasks = [
      [
          'id' => 1,
          'title' => 'Виконати лабораторну роботу №5 з PHP та масивів',
          'priority' => 'High',
          'is_completed' => false
      ],
      [
          'id' => 2,
          'title' => 'Вивчити відмінності між індексованими та асоціативними масивами',
          'priority' => 'Medium',
          'is_completed' => true
      ],
      [
          'id' => 3,
          'title' => 'Підготувати звіт у форматі Markdown (lab5.md)',
          'priority' => 'Low',
          'is_completed' => false
      ],
      [
          'id' => 4,
          'title' => 'Зробити коміт та відправити зміни на GitHub',
          'priority' => 'High',
          'is_completed' => false
      ]
  ];

  function formatTitle($text, $maxLength = 30) {
      if (mb_strlen($text, 'UTF-8') > $maxLength) {
          return mb_substr($text, 0, $maxLength, 'UTF-8') . "...";
      }
      return $text;
  }

  function getCurrentGreeting() {
      $hour = (int)date('H');
      if ($hour >= 6 && $hour <= 11) return "Доброго ранку";
      if ($hour >= 12 && $hour <= 17) return "Добрий день";
      if ($hour >= 18 && $hour <= 23) return "Добрий вечір";
      return "Доброї ночі";
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
        ul {
            list-style-type: square;
        }
    </style>
</head>
<body>
    <header>
        <h1><?= $appName ?></h1>
        <p class="greeting"><?= getCurrentGreeting() ?>!</p>
    </header>

    <main>
        <h2>Список завдань:</h2>
        <ul>
            <?php foreach ($tasks as $task): ?>
                <li class="<?= $task['is_completed'] ? 'task-done' : 'task-pending' ?>">
                    <strong><?= formatTitle($task['title']) ?></strong> 
                    <span>[Пріоритет: <?= $task['priority'] ?>]</span> — 
                    <span>
                        <?php if ($task['is_completed']): ?>
                             Виконано
                        <?php else: ?>
                             В процесі
                        <?php endif; ?>
                    </span>
                </li>
            <?php endforeach; ?>
        </ul>
    </main>
</body>
</html>