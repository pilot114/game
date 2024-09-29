<?php

namespace Game\RoleSystem;

enum AttrLevel
{
    case CRIPPLE;
    case LOW;
    case BELOW_AVERAGE;
    case AVERAGE;
    case ABOVE_AVERAGE;
    case OUTSTANDING;
    case STARTLING;

    static public function fromValue(int $value): self
    {
        return match (true) {
            $value < 7 => AttrLevel::CRIPPLE,
            $value < 8 => AttrLevel::LOW,
            $value < 10 => AttrLevel::BELOW_AVERAGE,
            $value < 11 => AttrLevel::AVERAGE,
            $value < 13 => AttrLevel::ABOVE_AVERAGE,
            $value < 15 => AttrLevel::OUTSTANDING,
            default => AttrLevel::STARTLING,
        };
    }
}