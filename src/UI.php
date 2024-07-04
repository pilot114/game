<?php

namespace Game;

class UI
{
    public function input($prompt): string
    {
        echo $prompt;
        return trim(fgets(STDIN));
    }

    public function output($message): void
    {
        echo $message;
    }
}