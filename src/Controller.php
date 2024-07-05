<?php

namespace Game;

class Controller
{
    const string SAVE_FILE = 'savegame.txt';

    public function __construct(
        private UI $ui,
        private Player $player,
        private World $world,
    ) {
    }

    public function start(): void
    {
        $this->ui->output("Welcome to the RPG Game!\n");
        $this->player->createCharacter();
        $this->world->generateWorld();
        $this->mainLoop();
    }

    private function mainLoop(): void
    {
        while (true) {
            $this->world->display();
            $command = $this->ui->input("Enter command: ");
            $this->processCommand($command);
        }
    }

    private function processCommand($command): void
    {
        match ($command) {
            'look' => $this->world->look(),
            'move' => $this->world->move(),
            'talk' => $this->world->talkToNpc(),
            'take' => $this->world->takeItem(),
            'inventory' => $this->player->showInventory(),
            'drop'      => $this->player->dropItem(),
            'quest'     => $this->player->showQuests(),
            'save' => $this->saveGame(),
            'load' => $this->loadGame(),
            default => $this->ui->output("Unknown command.\n"),
        };
    }

    private function saveGame(): void
    {
        $data = serialize([
            'player' => $this->player,
            'world' => $this->world,
        ]);
        file_put_contents(self::SAVE_FILE, $data);
        $this->ui->output("Game saved!\n");
    }

    private function loadGame(): void
    {
        if (!file_exists(self::SAVE_FILE)) {
            $this->ui->output("No saved game found.\n");
            return;
        }

        $data = file_get_contents(self::SAVE_FILE);
        $savedData = unserialize($data);
        $this->player = $savedData['player'];
        $this->world = $savedData['world'];
        $this->ui->output("Game loaded!\n");
    }
}