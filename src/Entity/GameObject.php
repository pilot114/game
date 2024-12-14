<?php

declare(strict_types=1);

namespace Game\Entity;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\BelongsTo;
use Cycle\Annotated\Annotation\Relation\RefersTo;
use Game\Enum\GameObjectType;

#[Entity]
class GameObject
{
    #[Column(type: "primary")]
    private int $id;

    public function __construct(
        #[Column(type: "string")]
        private string $name, // название
        #[Column(type: "string")]
        private string $description, // подробное описание
        #[Column(type: "integer")]
        private int $x, // позиция в локации по X
        #[Column(type: "integer")]
        private int $y, // позиция в локации по Y

        #[Column(type: "string")]
        private GameObjectType $type, // тип объекта
        #[BelongsTo(target: Location::class)]
        private Location $location, // локация

        #[RefersTo(target: GameObject::class, nullable: true)]
        private ?GameObject $parentGameObject = null, // в каком контейнере лежит
        #[BelongsTo(target: GameObjectTemplate::class, nullable: true)]
        private ?GameObjectTemplate $template = null,    // шаблон, если есть
    ) {
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'position' => [
                'x'  => $this->x,
                'y'  => $this->y,
            ],
            'type' => $this->type->name,
            'location' => $this->location->toArray(),
            'parentGameObject' => $this->parentGameObject?->toArray(),
            'template' => $this->template?->toArray(),
        ];
    }
}
