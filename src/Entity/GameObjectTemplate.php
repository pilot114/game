<?php

declare(strict_types=1);

namespace Game\Entity;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Game\Enum\GameObjectType;

#[Entity]
class GameObjectTemplate
{
    #[Column(type: "primary")]
    private int $id;

    public function __construct(
        #[Column(type: "string")]
        private string $name, // название
        #[Column(type: "string")]
        private string $description, // подробное описание
        #[Column(type: "string")]
        private GameObjectType $type, // тип объекта
    ) {
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'type' => $this->type->name,
        ];
    }
}
