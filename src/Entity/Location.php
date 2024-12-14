<?php

declare(strict_types=1);

namespace Game\Entity;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\RefersTo;

#[Entity]
class Location
{
    #[Column(type: "primary")]
    private int $id;

    public function __construct(
        #[Column(type: "string")]
        private string $name, // название
        #[Column(type: "string")]
        private string $description, // подробное описание

        #[RefersTo(target: Location::class, nullable: true)]
        private ?Location $parentLocation = null,
    ) {
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'parentLocation' => $this->parentLocation->toArray(),
        ];
    }
}
