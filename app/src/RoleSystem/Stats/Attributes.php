<?php

namespace Game\RoleSystem\Stats;

class Attributes
{
    // 1-20
    public function __construct(
        public int $strength = 10,
        public int $dexterity = 10,
        public int $intelligence = 10,
        public int $health = 10, // по сути - выносливость ?
    ) {
    }

    public function up(string $name, int $points): void
    {
        if ($name === 'strength' || $name === 'health') {
            $points = 10; // -10
        }
        if ($name === 'dexterity' || $name === 'intelligence') {
            $points = 20; // -20
        }
    }

    // вторичные характеристики

    // базовый груз в фунтах (0.54 кг)
    public function baseLoad(): float
    {
        return ($this->strength * $this->strength) / 5;
    }

    public function healthPoint(): int
    {
        return $this->strength;
    }

    // воля
    public function will(): int
    {
        return $this->intelligence;
    }

    public function perception(): int
    {
        return $this->intelligence;
    }

    public function fatigue(): int
    {
        return $this->health;
    }

    public function speed(): float
    {
        return ($this->health + $this->dexterity) / 4;
    }

    // уклонение
    public function evasion(): int
    {
        return (int)(floor($this->speed())) + 3;
    }

    public function movement(): int
    {
        return (int)(floor($this->speed()));
    }

    // возвращает 2 значения: от прямой атаки и амплитудной
    public function damage(): array
    {
        return match ($this->strength) {
            1, 2 => [mt_rand(1, 6) - 6, mt_rand(1, 6) - 5],
            3, 4 => [mt_rand(1, 6) - 5, mt_rand(1, 6) - 4],
            5, 6 => [mt_rand(1, 6) - 4, mt_rand(1, 6) - 3],
            7, 8 => [mt_rand(1, 6) - 3, mt_rand(1, 6) - 2],
            9 => [mt_rand(1, 6) - 2, mt_rand(1, 6) - 1],
            10 => [mt_rand(1, 6) - 2, mt_rand(1, 6)],
            11 => [mt_rand(1, 6) - 1, mt_rand(1, 6) + 1],
            12 => [mt_rand(1, 6) - 1, mt_rand(1, 6) + 2],
            13 => [mt_rand(1, 6), mt_rand(1, 6) + mt_rand(1, 6) - 1],
            14 => [mt_rand(1, 6), mt_rand(1, 6) + mt_rand(1, 6)],
            15 => [mt_rand(1, 6) + 1, mt_rand(1, 6) + mt_rand(1, 6) + 1],
            16 => [mt_rand(1, 6) + 1, mt_rand(1, 6) + mt_rand(1, 6) + 2],
            17 => [mt_rand(1, 6) + 2, mt_rand(1, 6) + mt_rand(1, 6) + mt_rand(1, 6) - 1],
            18 => [mt_rand(1, 6) + 2, mt_rand(1, 6) + mt_rand(1, 6) + mt_rand(1, 6)],
            19 => [mt_rand(1, 6) + mt_rand(1, 6) - 1, mt_rand(1, 6) + mt_rand(1, 6) + mt_rand(1, 6) + 1],
            20 => [mt_rand(1, 6) + mt_rand(1, 6) - 1, mt_rand(1, 6) + mt_rand(1, 6) + mt_rand(1, 6) + 2],
        };
    }
}