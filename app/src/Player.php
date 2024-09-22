<?php

namespace Game;

use Game\UI\UI;

class Player
{
    public string $name;
    public int $level;
    /** @var Collection<Item> */
    public Collection $inventory;
    /** @var Collection<Quest> */
    public Collection $quests;
    public int $health;
    public int $attack;

    public function __construct(
        private readonly UI $ui,
    ) {
        $this->inventory = new Collection();
        $this->quests = new Collection();
    }

    public function createCharacter(): void
    {
        $this->name = $this->ui->input("Enter your character's name: ");
        $this->level = 1;
        $this->health = 20;
        $this->attack = 5;
        $this->addItem(new Item("Sword", "A basic sword"));
        $this->addItem(new Item("Shield", "A basic shield"));
        $this->addItem(new Item("Health Potion", "Restores health"));
        $this->ui->output("Character created: {$this->name}, Level: {$this->level}\n");
    }

    public function addItem(Item $item): void
    {
        $this->inventory->add($item);
        $this->ui->output("You have received: $item\n");
    }

    public function removeItem(Item $item): void
    {
        $this->inventory->remove($item);
        $this->ui->output("You have removed: $item\n");
    }

    public function showInventory(): void
    {
        if ($this->inventory->isEmpty()) {
            $this->ui->output("Your inventory is empty.\n");
        } else {
            $this->ui->output("Your inventory: " . $this->inventory . "\n");
        }
    }

    public function dropItem(): void
    {
        $name = $this->ui->input("Which item do you want to drop? ");
        $item = $this->inventory->getBy('name', $name);
        if ($item) {
            $this->removeItem($item);
        } else {
            $this->ui->output("You don't have an item named '$name'.\n");
        }
    }

    public function addQuest(Quest $quest): void
    {
        $this->quests->add($quest);
        $this->ui->output("You have taken the quest: $quest\n");
    }

    public function showQuests(): void
    {
        if ($this->quests->isEmpty()) {
            $this->ui->output("You have no quests.\n");
        } else {
            $this->ui->output("Your quests: " . $this->quests . "\n");
        }
    }

    public function completeQuestTask(Task $task): void
    {
        foreach ($this->quests->getAll() as $quest) {
            if (!$quest->isCompleted) {
                $taskCompleted = $quest->tryCompleteTask($task);
                if (!$taskCompleted) {
                    continue;
                }
                $this->ui->output("Task '$task' completed for quest '{$quest->title}'.\n");
                if ($quest->isCompleted) {
                    $this->ui->output("Quest '{$quest->title}' completed!\n");
                }
            }
        }
    }
}
