<?php

namespace Game\Enum;

enum GameObjectType: string
{
    // Creatures
    case Player = 'Player';
    case NPC = 'NPC';
    case Beast = 'Beast';
    case Monster = 'Monster';
    case Ghost = 'Ghost';
    // Items
    case Item = 'Item';
    case Armor = 'Armor';
}