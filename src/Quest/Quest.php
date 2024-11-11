<?php

namespace Game\Quest;

use Game\Collection;
use Game\Player;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

// компания - приключение - встреча

class Quest
{
    public function __construct(
        // название в игре
        public readonly string $name,
        // подробное описание в игре
        public readonly string $description,
        // описание для LLM
        public readonly string $info,
        /** @var array<int, Task> */
        public array $tasks,
        public bool $isCompleted = false,
    ) {
    }

    public function setContext(
        EventDispatcher $dispatcher,
        ExpressionLanguage $language,
        Player $player
    ) {

    }

    public function tryCompleteTask(Task $task): bool
    {
        $tasks = new Collection($this->tasks);
        $task = $tasks->getBy('title', $task->message);
        if ($task === null) {
            return false;
        }
        $tasks->remove($task);
        if ($tasks->isEmpty()) {
            $this->isCompleted = true;
        }
        return true;
    }

    public function __toString(): string
    {
        return $this->name . " - " . $this->description;
    }
}