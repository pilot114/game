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

    public function accept($prompt): bool
    {
        $answer = $this->input("$prompt (yes/no) ");
        return in_array(strtolower($answer), ['yes', 'y']);
    }
}