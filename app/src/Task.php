<?php

namespace Game;

class Task
{
    public function __construct(
        public string $title,
        public TaskAction $action,
    ) {
    }

    public function __toString(): string
    {
        return $this->action->name . ' ' . $this->title;
    }
}