<?php

namespace Game;

use Game\UI\UI;
use Symfony\Component\Yaml\Yaml;

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
        $worldData = Yaml::parseFile(__DIR__ . '/world.yaml');

        foreach ($worldData['locations'] as $locationData) {
            $location = new Location($locationData['name'], $locationData['description']);

            foreach ($locationData['monsters'] as $monsterData) {
                $monster = new Monster($monsterData['name'], $monsterData['level'], $monsterData['health'], $monsterData['attack']);
                foreach ($monsterData['loot'] as $lootData) {
                    $monster->addLoot(new Item($lootData['name'], $lootData['description']));
                }
                $location->addMonster($monster);
            }

            foreach ($locationData['items'] as $itemData) {
                $location->addItem(new Item($itemData['name'], $itemData['description']));
            }

            foreach ($locationData['npcs'] as $npcData) {
                $npc = new Npc($npcData['name'], $npcData['dialogue']);
                foreach ($npcData['quests'] as $questData) {
                    $tasks = new Collection();
                    foreach ($questData['tasks'] as $taskData) {
                        $taskAction = TaskAction::tryFrom($taskData['action']);
                        $tasks->add(new Task($taskData['title'], $taskAction));
                    }
                    $npc->addQuest(new Quest($questData['title'], $questData['description'], $tasks));
                }
                $location->addNpc($npc);
            }

            $this->locations->add($location);
        }

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
            $this->ui->output("You attack the $monster for {$this->player->attack} damage. Monster health: {$monster->health}\n");

            if ($monster->health <= 0) {
                $this->ui->output("You defeated the $monster!\n");

                foreach ($monster->loot->getAll() as $item) {
                    $this->player->addItem($item);
                }
                $location->removeMonster($monster);

                $this->player->completeQuestTask(new Task($monster->name, TaskAction::KILL));

                return;
            }

            // Monster attacks player
            $this->player->health -= $monster->attack;
            $this->ui->output("The $monster attacks you for {$monster->attack} damage. Your health: {$this->player->health}\n");

            if ($this->player->health <= 0) {
                $this->ui->output("You were defeated by the $monster...\n");
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

            $this->player->completeQuestTask(new Task($item->name, TaskAction::TAKE));
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
            $this->ui->output("You talk to $npc. They say: '{$npc->dialogue}'.\n");

            if (!$npc->quests->isEmpty()) {
                foreach ($npc->quests->getAll() as $quest) {
                    $this->ui->output("Quest available: $quest\n");
                    if ($this->ui->accept("Do you want to take this quest?")) {
                        $this->player->addQuest($quest);
                    }
                }
            }

            $this->player->completeQuestTask(new Task($npc->name, TaskAction::TALK));
        } else {
            $this->ui->output("Unknown location.\n");
        }
    }

    private function getLocationByName(string $name): ?Location
    {
        return $this->locations->getBy('name', $name);
    }
}
