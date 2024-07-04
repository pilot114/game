<?php

namespace Game;

class Monster
{
    /** @var Collection<Item> */
    public Collection $loot;

    public function __construct(
        public string $name,
        public int $level,
        public int $health,
        public int $attack
    ) {
        $this->loot = new Collection();
    }

    public function addLoot(Item $item): void
    {
        $this->loot->add($item);
    }

    public function __toString(): string
    {
        return $this->name;
    }
}