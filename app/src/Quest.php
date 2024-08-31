<?php

namespace Game;

class Quest
{
    public bool $isCompleted;

    /**
     * @param Collection<Task> $tasks
     */
    public function __construct(
        public string $title,
        public string $description,
        public Collection $tasks
    ) {
        $this->isCompleted = false;
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
        return $this->title . " - " . $this->description . " (Tasks left: " . $this->tasks . ")";
    }
}
