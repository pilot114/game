<?php

namespace Game;

class Location
{
    public function __construct(
        public string $name,
        public string $description,
        public array $monsters = []
    ) {
    }

    public function addMonster(Monster $monster): void
    {
        $this->monsters[] = $monster;
    }

    public function removeMonster(Monster $monster): void
    {
        $index = array_search($monster, $this->monsters);
        if ($index !== false) {
            unset($this->monsters[$index]);
            $this->monsters = array_values($this->monsters);
        }
    }

    public function __toString(): string
    {
        return $this->name;
    }
}