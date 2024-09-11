<?php

use Game\Collection;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\Yaml\Yaml;

include 'vendor/autoload.php';

/**
 * Скрипт для генерации квестов
 */

class Task
{
    public function __construct(
        public string $trigger,
        public ?string $journal = null,
        public ?string $set = null,
        public ?string $message = null,
        public ?string $spawn = null,
    ) {
    }

    public function __toString(): string
    {
        return $this->trigger;//mb_strtolower($this->action->name) . ' ' . $this->title;
    }
}

class Quest
{
    public bool $isCompleted = false;

    /**
     * @param Collection<Task> $tasks
     */
    public function __construct(
        public int $id,
        public string $title,
        public string $description,
        public Collection $tasks,
        public EventDispatcher $dispatcher,
        public ExpressionLanguage $language,
    ) {
        $this->tasks = $tasks->isEmpty() ? new Collection() : $tasks;
    }

    public function tryCompleteTask(Task $task): bool
    {
        $task = $this->tasks->getBy('title', $task->title);
        if ($task === null) {
            return false;
        }
        $this->tasks->remove($task);
        if ($this->tasks->isEmpty()) {
            $this->isCompleted = true;
        }
        return true;
    }

    public function __toString(): string
    {
        return $this->title . " - " . $this->description;
    }
}

$quests = Yaml::parseFile(__DIR__ . '/resources/data/quest.yaml');
foreach ($quests as $questId => $quest) {
    dump($quest);
    die();

    $tasks = new Collection();
    foreach ($quest['tasks'] as $task) {
        $tasks->add(new Task(...$task));
    }

// https://core-rpg.net/articles/analytics/genre/sozdanie_razvetvljonnoj_dialogovoj_sistemy_chast_1_vstuplenie
// Player
    $quest = new Quest(
        id: $questId,
        title: $quest['title'],
        description: $quest['description'],
        tasks: $tasks,
        dispatcher: new EventDispatcher(),
        language: new ExpressionLanguage(),
    );

    echo $quest . "\n";
}
