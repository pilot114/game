<?php

namespace Game\Quest;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class Location
{
    private EventDispatcher $dispatcher;
    private ExpressionLanguage $expressionLanguage;

    /** @var array<0: ?int, 1: ?int> */
    public array $grid;
    public int $scale;

    public function __construct(
        // название в игре
        public readonly string $name,
        // подробное описание в игре
        public readonly string $description,
        // описание для LLM
        public readonly string $info,
        string $grid,
        /** @var array<string, Entity> */
        public array $entities = [],
        /** @var array<string, NPC> */
        public array $npc = [],
        /** @var array<string, Quest> */
        public array $quests = [],
    ) {
        foreach (explode(' ', $grid) as $position) {
            @[$name, $x, $y] = explode(':', $position);
            $this->grid[$name] = [$x, $y];
        }
        $this->expressionLanguage = new ExpressionLanguage();
    }

    public function setDispatcher(EventDispatcher $dispatcher): void
    {
        $this->dispatcher = $dispatcher;
    }

    public function getActiveQuests(): array
    {
        foreach ($this->quests as $id => $quest) {
            dump($id);
            dump($quest);
            die();
        }
    }
}