<?php

namespace Game;

class Monster
{
    public array $loot;

    public function __construct(
        public string $name,
        public int $level,
        public int $health,
        public int $attack
    ) {
        $this->loot = [];
    }

    public function addLoot(Item $item): void
    {
        $this->loot[] = $item;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}