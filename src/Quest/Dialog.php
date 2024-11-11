<?php

namespace Game\Quest;

class Dialog
{
    /**
     * @param array<string, Dialog|string> $options
     */
    public function __construct(
        public array $options,
    ) {
    }
}