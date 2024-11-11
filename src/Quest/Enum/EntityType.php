<?php

namespace Game\Quest\Enum;

enum EntityType: string
{
    case Item = 'item';
    case Container = 'container';
    case Person = 'person';
    case Common = 'common';
    case Book = 'book';
}