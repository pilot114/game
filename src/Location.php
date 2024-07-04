<?php

namespace Game;

class Location
{
    /** @var Collection<Monster> */
    public Collection $monsters;
    /** @var Collection<Item> */
    public Collection $items;
    /** @var Collection<Npc> */
    public Collection $npcs;

    public function __construct(
        public string $name,
        public string $description,
    ) {
        $this->monsters = new Collection();
        $this->items = new Collection();
        $this->npcs = new Collection();
    }

    public function addMonster(Monster $monster): void
    {
        $this->monsters->add($monster);
    }

    public function removeMonster(Monster $monster): void
    {
        $this->monsters->remove($monster);
    }

    public function addItem(Item $item): void
    {
        $this->items->add($item);
    }

    public function removeItem(Item $item): void
    {
        $this->items->remove($item);
    }

    public function addNpc(Npc $npc): void
    {
        $this->npcs->add($npc);
    }

    public function removeNpc(Npc $npc): void
    {
        $this->npcs->remove($npc);
    }

    public function __toString(): string
    {
        return $this->name;
    }
}