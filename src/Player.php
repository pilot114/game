<?php

namespace Game;

class Player
{
    public string $name;
    public int $level;
    public array $inventory;
    public int $health;
    public int $attack;

    public function __construct(
        private UI $ui,
    ) {
        $this->inventory = [];
    }

    public function createCharacter(): void
    {
        $this->name = $this->ui->input("Enter your character's name: ");
        $this->level = 1;
        $this->health = 20;
        $this->attack = 5;
        $this->inventory = [];
        $this->addItem(new Item("Sword", "A basic sword"));
        $this->addItem(new Item("Shield", "A basic shield"));
        $this->addItem(new Item("Health Potion", "Restores health"));
        $this->ui->output("Character created: {$this->name}, Level: {$this->level}\n");
        $this->ui->output("Starting items: " . implode(", ", $this->inventory) . "\n");
    }

    public function addItem(Item $item): void
    {
        $this->inventory[] = $item;
        $this->ui->output("You have received: $item\n");
    }

    public function showInventory(): void
    {
        if (empty($this->inventory)) {
            $this->ui->output("Your inventory is empty.\n");
        } else {
            $this->ui->output("Your inventory: " . implode(", ", $this->inventory) . "\n");
        }
    }
}