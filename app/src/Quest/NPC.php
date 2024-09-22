<?php

namespace Game\Quest;

use Game\Quest\Enum\EntityType;

class NPC
{
    /** @var array<EntityType>  */
    public array $types;

    /**
     * @param array<int, string> $replics
     * @param array<int> $trade
     * @param ?string $education
     */
    public function __construct(
        // название в игре
        public readonly string $name,
        // подробное описание в игре
        public readonly string $description,
        // описание для LLM
        public readonly string $info,
        string $types = '',
        public readonly array $replics = [],
        public readonly array $trade = [],
        public readonly ?string $education = null,
        public readonly ?Dialog $dialog = null,
    ) {
        $this->types = explode(' ', $types);
    }
}