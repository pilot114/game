<?php

namespace Game;

class Npc
{
    /** @var Collection<Quest> */
    public Collection $quests;

    public function __construct(
        public string $name,
        public string $dialogue,
    ) {
        $this->quests = new Collection();
    }

    public function addQuest(Quest $quest): void
    {
        $this->quests->add($quest);
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
