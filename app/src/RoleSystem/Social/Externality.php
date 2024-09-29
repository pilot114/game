<?php

namespace Game\RoleSystem\Social;

// внешность
enum Externality
{
    case DISGUSTING; // -4 реакции, -16 очков для распределения
    case UGLY; // -2 реакции, -8 очков для распределения
    case UNATTRACTIVE; // -1, -4
    case NORMAL;
    case ATTRACTIVE; // +1, +4
    case BEAUTIFUL; // +4 другой пол/+2, +12
    case VERY_BEAUTIFUL; // +6 другой пол/+2, +16
}