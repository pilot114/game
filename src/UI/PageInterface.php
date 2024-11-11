<?php

namespace Game\UI;

use PhpTui\Term\Event;

interface PageInterface
{
    public function handle(Event $event): ?PageEvent;
    public function draw(): void;
}
