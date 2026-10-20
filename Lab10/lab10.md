Task.php
```php
<?php
declare(strict_types=1);

class Task
{
    public function __construct(                //Конструктор класу ініціалізує властивості об'єкта за допомогою COnstructor promotion та, перевіряє чи не є навза завдання порожньою.
        public readonly int    $id,
        private string         $title,
        private bool           $isCompleted = false,
        private ?string        $deadline    = null,
    ) {
        if (empty(trim($title))) {
            throw new InvalidArgumentException('Назва завдання не може бути порожньою.');
        }
    }

    public function getId(): int { return $this->id; }          //getId повертає унікальний ідентифікатор завдання.
    public function getTitle(): string { return $this->title; }             //getTitle повертає назву завдання.
    public function isCompleted(): bool { return $this->isCompleted; }       //isCompleted повертає логічне значення, що вказує, чи виконане завдання. 
    public function getDeadline(): ?string { return $this->deadline; }          //getDeadline повертає дату дедлайну завдання у вигляді рядка або null, якщо дата не задана.

    public function isOverdue(): bool               //перевіряє, чи минув термін виконання завдання за умови, що воно ще не виконане.
    {
        if ($this->isCompleted || $this->deadline === null) {
            return false;
        }
        return strtotime($this->deadline) < time();
    }

    public function complete(): void { $this->isCompleted = true; }             //complete змінює статус завдання на виконане, встановлюючи прапорець.

    public function getStatusLabel(): string            //getStatusLabel повертає текстову мітку статусу завдання залежно від його стану.
    {
        if ($this->isCompleted) return 'Виконано';
        if ($this->isOverdue())  return 'Прострочено';
        return 'В процесі';
    }

    public function toArray(): array            //toArray перетворює об'єкт завдання на звичайний асоціативний масив для подальшого збереження у форматі JSON.
    {
        return [
            'id'          => $this->id,
            'title'       => $this->title,
            'isCompleted' => $this->isCompleted,
            'deadline'    => $this->deadline,
        ];
    }

    public static function fromArray(array $data): self             //fromArray статичний метод-фабрика, який створює новий об'єкт Task на основі масиву даних, зчитаного з файлу сховища.
    {
        return new self(
            id:          $data['id'],
            title:       $data['title'],
            isCompleted: $data['isCompleted'] ?? false,
            deadline:    $data['deadline']    ?? null,
        );
    }
}
```

TaskRepository.php
```php
<?php
declare(strict_types=1);

require_once __DIR__ . '/Task.php';

class TaskRepository
{
    /** @var Task[] */
    private array $tasks = [];

    public function __construct(private string $storageFile)            //__construct приймає шлях до файлу сховища, ініціалізує його та одразу викликає метод завантаження даних.
    {
        $this->load();
    }

    public function findAll(): array            //findAll повертає масив усіх наявних об'єктів завдань у репозиторії.
    { 
        return $this->tasks; 
    }

    public function findById(int $id): ?Task            //findById шукає та повертає конкретних об'єкт Task за його унікальним ідентифікатором або null, якщо його не знайдено.
    {
        foreach ($this->tasks as $task) {
            if ($task->getId() === $id) return $task;
        }
        return null;
    }

    public function add(string $title, ?string $deadline = null): Task                  //add створює новий об'єкт Task із наступним вільним id, додає його до загальної колекції, зберігає зміни у файл і повертаж створений об'єкт.
    {
        $task = new Task($this->generateNextId(), $title, false, $deadline);
        $this->tasks[] = $task;
        $this->save();
        return $task;
    }

    public function complete(int $id): bool             //complete знаходить завдання за id, змінює його статус на виконане за допомогою методів об'єкта та оновлює сховище.
    {
        $task = $this->findById($id);
        if (!$task) return false;
        $task->complete();
        $this->save();
        return true;
    }

    public function delete(int $id): bool               //delete видаляє завдання з масиву за його id, оновлює індексацію масиву та перезаписує файл JSON.
    {
        foreach ($this->tasks as $key => $task) {
            if ($task->getId() === $id) {
                unset($this->tasks[$key]);
                $this->tasks = array_values($this->tasks);
                $this->save();
                return true;
            }
        }
        return false;
    }

    public function countCompleted(): int               //countCompleted підраховує загальну кількість виконаних завдань у колекції за допомогою фільтрації.
    {
        return count(array_filter($this->tasks, fn(Task $t) => $t->isCompleted()));
    }

    public function countOverdue(): int                 //countOverdue підраховує кількість прострочених невиконаних завдань.
    {
        return count(array_filter($this->tasks, fn(Task $t) => $t->isOverdue()));
    }

    private function generateNextId(): int              //generateNextId приватний допоміжний метод, який генерує наступний унікальний числовий ID на основі існуючих завдань.
    {
        if (empty($this->tasks)) return 1;
        return max(array_map(fn(Task $t) => $t->getId(), $this->tasks)) + 1;
    }

    private function load(): void               //load приватний метод, який зчитує дані з JSON-файлу та десеріалізує їх у масив об'єктів Task.
    {
        if (!file_exists($this->storageFile)) { 
            $this->tasks = []; 
            return; 
        }
        $data = json_decode(file_get_contents($this->storageFile), true) ?? [];
        $this->tasks = array_map(fn(array $item) => Task::fromArray($item), $data);
    }

    private function save(): void               //save приватний метод, який серіалізує колекцію об'єктів у масиви та записує їх назад у файл сховища у форматі JSON.
    {
        $data = array_map(fn(Task $t) => $t->toArray(), $this->tasks);
        file_put_contents(
            $this->storageFile,
            json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );
    }
}
```

Що таке клас і чим він відрізняється від звичайного масиву у PHP? Чому завдання зручніше представити об’єктом класу Task, ніж масивом ['id' => 1, 'title' => '...']?

Клас — це шаблон (креслення) для створення об'єктів, що об'єднує дані та методи їх обробки. На відміну від масиву, об'єкт захищає дані від випадкових змін.

Що таке private-властивість і навіщо $title у класі Task зроблено private? Як зовнішній код може прочитати її значення?

private обмежує доступ до властивості лише всередині самого класу, захищаючи $title від прямих модифікацій ззовні. Зовнішній код читає її значення через публічний метод getTitle().

Поясніть, що відбувається у рядку public readonly int $id у конструкторі. Чому $id є readonly, а $isCompleted — ні?

readonly дозволяє встановити значення властивості лише один раз у конструкторі, після чого воно стає незмінним. id не повинно змінюватися протягом життєвого циклу завдання, тоді як статус $isCompleted оновлюється під час виконання.

Що робить статичний метод Task::fromArray($data)? Для чого він оголошений як static, а не як звичайний метод?

Task::fromArray($data) створює новий об'єкт Task на основі масиву даних (наприклад, з JSON-файлу). Метод оголошено як static, оскільки він є фабрикою і викликається через ім'я класу до створення самого об'єкта.

Опишіть «ланцюжок» збереження даних: що відбувається під капотом від моменту виклику $repo->add('Нове завдання') до появи нового рядка у файлі tasks.json?

Метод add() створює новий об'єкт Task, додає його до масиву в пам'яті ($this->tasks), після чого викликає save(), який перетворює об'єкти на масиви (toArray()), кодує у формат JSON (json_encode) і записує на диск (file_put_contents).