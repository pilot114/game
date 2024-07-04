<?php

namespace Game;

class Quest
{
    public function __construct(
        public string $title,
        public string $description,
        public bool $isCompleted = false
    ) {
    }

    public function __toString(): string
    {
        return $this->title;
    }
}
