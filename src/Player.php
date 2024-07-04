<?php

namespace Game;

class Player
{
    public string $name;
    public int $level;
    /** @var Collection<Item> */
    public Collection $inventory;
    public int $health;
    public int $attack;

    public function __construct(
        private readonly UI $ui,
    ) {
        $this->inventory = new Collection();
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
        $this->ui->output("Starting items: " . $this->inventory . "\n");
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
}
