<?php

namespace Game;

enum TaskAction: string
{
    case KILL = 'KILL';
    case TAKE = 'TAKE';
    case TALK = 'TALK';
}
