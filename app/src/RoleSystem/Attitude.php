<?php

namespace Game\RoleSystem;

enum Attitude
{
    case TERRIBLE;
    case VERY_BAD;
    case BAD;
    case WEAK;
    case NEUTRAL;
    case GOOD;
    case VERY_GOOD;
    case EXCELLENT;

    static public function fromValue(int $value): self
    {
        return match (true) {
            $value <= 0 => Attitude::TERRIBLE,
            $value <= 3 => Attitude::VERY_BAD,
            $value <= 6 => Attitude::BAD,
            $value <= 9 => Attitude::WEAK,
            $value <= 12 => Attitude::NEUTRAL,
            $value <= 15 => Attitude::GOOD,
            $value <= 18 => Attitude::VERY_GOOD,
            default => Attitude::EXCELLENT,
        };
    }
}