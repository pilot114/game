<?php

namespace Game;

class World
{
    /** @var Collection<Location> */
    private Collection $locations;

    public function __construct(
        private UI $ui,
        private Player $player,
    ) {
        $this->locations = new Collection();
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

        $sword = new Item('Old Sword', 'An old rusty sword');
        $forest->addItem($sword);

        $villager = new Npc('Villager', 'A friendly villager');
        $villager->addQuest(new Quest('Find sheep', 'Find my lost sheep'));
        $village->addNpc($villager);

        $this->locations->add($forest);
        $this->locations->add($village);
        $this->locations->add($dungeon);

        $this->ui->output("World generated with locations: " . $this->locations . "\n");
    }

    public function display(): void
    {
        $this->ui->output("You are in a mysterious world.\n");
    }

    public function look(): void
    {
        $this->ui->output("You see: " . $this->locations . "\n");
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
        if ($location->monsters->isEmpty()) {
            $this->ui->output("This location is peaceful.\n");
            return;
        }

        $monster = $location->monsters->getRandom();
        $this->ui->output("You encounter a {$monster}!\n");

        while ($monster->health > 0 && $this->player->health > 0) {
            // Player attacks monster
            $monster->health -= $this->player->attack;
            $this->ui->output("You attack the {$monster->name} for {$this->player->attack} damage. Monster health: {$monster->health}\n");

            if ($monster->health <= 0) {
                $this->ui->output("You defeated the {$monster->name}!\n");

                // Give player loot
                foreach ($monster->loot->getAll() as $item) {
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

    public function takeItem(): void
    {
        $locationName = $this->ui->input("Where do you want to search for items? ");
        $location = $this->getLocationByName($locationName);

        if ($location) {
            if ($location->items->isEmpty()) {
                $this->ui->output("No items found in $location.\n");
                return;
            }

            $item = $location->items->getRandom();
            $this->player->addItem($item);
            $location->removeItem($item);
        } else {
            $this->ui->output("Unknown location.\n");
        }
    }

    public function talkToNpc(): void
    {
        $locationName = $this->ui->input("Where do you want to look for NPCs? ");
        $location = $this->getLocationByName($locationName);

        if ($location) {
            if ($location->npcs->isEmpty()) {
                $this->ui->output("No NPCs found in $location.\n");
                return;
            }

            $npc = $location->npcs->getRandom();
            $this->ui->output("You talk to {$npc->name}. They say: '{$npc->dialogue}'.\n");

            if (!$npc->quests->isEmpty()) {
                foreach ($npc->quests->getAll() as $quest) {
                    $this->ui->output("Quest available: $quest\n");
                }
            }
        } else {
            $this->ui->output("Unknown location.\n");
        }
    }

    public function showQuests(): void
    {
        // Add logic to display quests to the player
    }

    private function getLocationByName(string $name): ?Location
    {
        return $this->locations->getBy('name', $name);
    }
}