<?php

namespace Game;

class Item
{
    public function __construct(
        public string $name,
        public string $description
    ) {
    }

    public function __toString(): string
    {
        return "\033[33m" . $this->name . "\033[0m";
    }
}