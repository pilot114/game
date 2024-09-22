<?php

namespace Game\Quest;

use Game\Quest\Enum\EntityType;

class Entity
{
    /** @var array<EntityType>  */
    public array $types;

    public function __construct(
        // название в игре
        public readonly string $name,
        // подробное описание в игре
        public readonly string $description,
        // описание для LLM
        public readonly string $info,
        string $types = '',
    ) {
        $this->types = explode(' ', $types);
    }
}