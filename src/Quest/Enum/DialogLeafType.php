<?php

namespace Game\Quest\Enum;

enum DialogLeafType: string
{
    case Up = 'up';
    case Start = 'start';
    case End = 'end';
    case Trade = 'trade';
    case Battle = 'battle';
    case Drop = 'drop';
}
