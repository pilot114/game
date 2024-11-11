<?php

namespace Game\RoleSystem\Social;

enum Wealth
{
    case POOR; // 0.2 от среднего, -15 очков
    case NOT_RICH; // 0.5 от среднего, -10 очков
    case MIDDLE;
    case SECURED; // x5, 10 очков
    case RICH; // x20, 20 очков
    case VERY_RICH; // x100, 50 очков
}