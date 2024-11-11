<?php

namespace Game\RoleSystem;

enum Level
{
    case WEAK;
    case SIMPLE;
    case COMPETENT;
    case EXTRAORDINARY;
    case HERO;
    case GREAT;
    case LEGENDARY;

    static public function fromValue(int $value): self
    {
        return match (true) {
            $value < 25 => Level::WEAK,
            $value <= 50 => Level::SIMPLE,
            $value <= 75 => Level::COMPETENT,
            $value <= 100 => Level::EXTRAORDINARY,
            $value <= 200 => Level::HERO,
            $value <= 300 => Level::GREAT,
            default => Level::LEGENDARY,
        };
    }
}