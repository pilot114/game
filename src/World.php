<?php

namespace Game;

class World
{
    private array $locations;

    public function __construct(
        private UI $ui,
        private Player $player,
    ) {
    }

    public function generateWorld(): void
    {
        $forest = new Location('Forest', 'A dense and dark forest.');
        $village = new Location('Village', 'A small peaceful village.');
        $dungeon = new Location('Dungeon', 'A dangerous dungeon full of monsters.');

        $goblin = new Monster('Goblin', 1, 10, 2);
        $goblin->addLoot(new Item('Goblin Ear', 'A trophy from a Goblin'));

        $bandit = new Monster('Bandit', 2, 15, 3);
        $bandit->addLoot(new Item('Bandit Dagger', 'A small dagger used by Bandits'));

        $dragon = new Monster('Dragon', 10, 100, 20);
        $dragon->addLoot(new Item('Dragon Scale', 'A rare scale from a Dragon'));

        $forest->addMonster($goblin);
        $village->addMonster($bandit);
        $dungeon->addMonster($dragon);

        $this->locations = [$forest, $village, $dungeon];

        $this->ui->output("World generated with locations: " . implode(", ", $this->locations) . "\n");
    }

    public function display(): void
    {
        $this->ui->output("You are in a mysterious world.\n");
    }

    public function look(): void
    {
        $this->ui->output("You see: " . implode(", ", $this->locations) . "\n");
    }

    public function move(): void
    {
        $locationName = $this->ui->input("Where do you want to go? ");
        $location = $this->getLocationByName($locationName);

        if ($location) {
            $this->ui->output("You moved to the $location.\n");
            $this->encounter($location);
        } else {
            $this->ui->output("Unknown location.\n");
        }
    }

    private function encounter(Location $location): void
    {
        if (empty($location->monsters)) {
            return;
        }

        $monster = $location->monsters[array_rand($location->monsters)];
        $this->ui->output("You encounter a {$monster}!\n");

        while ($monster->health > 0 && $this->player->health > 0) {
            // Player attacks monster
            $monster->health -= $this->player->attack;
            $this->ui->output("You attack the {$monster->name} for {$this->player->attack} damage. Monster health: {$monster->health}\n");

            if ($monster->health <= 0) {
                $this->ui->output("You defeated the {$monster->name}!\n");

                // Give player loot
                foreach ($monster->loot as $item) {
                    $this->player->addItem($item);
                }
                $location->removeMonster($monster);

                return;
            }

            // Monster attacks player
            $this->player->health -= $monster->attack;
            $this->ui->output("The {$monster->name} attacks you for {$monster->attack} damage. Your health: {$this->player->health}\n");

            if ($this->player->health <= 0) {
                $this->ui->output("You were defeated by the {$monster->name}...\n");
                $this->ui->output("Game over.\n");
                exit; // End game
            }
        }
    }

    private function getLocationByName(string $name): ?Location
    {
        foreach ($this->locations as $location) {
            if ($location->name === $name) {
                return $location;
            }
        }
        return null;
    }
}