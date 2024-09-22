<?php

namespace Game\Quest;

class Task
{
    public function __construct(
        public string $trigger,
        public string $journal,
        public ?string $message = null,
        public ?string $set = null,
    ) {
    }
}