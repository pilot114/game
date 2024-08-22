<?php

namespace Game;

trait GameProgress
{
    const SAVE_FILE = 'save.txt';

    private function saveGame(): void
    {
        $data = serialize([
            'player' => $this->player,
            'world' => $this->world,
        ]);
        file_put_contents(self::SAVE_FILE, $this->xor($data));
        $this->ui->output("Игра сохранена!\n");
    }

    private function loadGame(): void
    {
        if (!file_exists(self::SAVE_FILE)) {
            $this->ui->output("Не найдено сохранённой игры\n");
            return;
        }

        $data = $this->xor(file_get_contents(self::SAVE_FILE));
        $savedData = unserialize($data);
        $this->player = $savedData['player'];
        $this->world = $savedData['world'];
        $this->ui->output("Игра загружена!\n");
    }

    private function xor(string $plain): string
    {
        $key = (string)0x600dc0de;
        for($i = 0; $i < strlen($plain); $i++) {
            $plain[$i] = ($plain[$i] ^ $key[$i % strlen($key)]);
        }
        return $plain;
    }
}
