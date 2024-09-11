<?php

namespace Game\Person;

class Chars
{
    public function __construct(
        public int $strength,
        public int $dexterity,
        public int $constitution,
        public int $charisma,
        public int $intelligence,
        public int $wisdom,
    ) {
    }

    public function getModificator(string $name): int
    {
        return match ($this->$name) {
            1 => -5,
            2,3 => -4,
            4,5 => -3,
            6,7 => -2,
            8,9 => -1,
            10,11 => 0,
            12,13 => 1,
            14,15 => 2,
            16,17 => 3,
            18,19 => 4,
            20,21 => 5,
            22,23 => 6,
            24,25 => 7,
            26,27 => 8,
            28,29 => 9,
            30 => 10,
        };
    }
}