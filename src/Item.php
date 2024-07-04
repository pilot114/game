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
        return $this->name;
    }
}