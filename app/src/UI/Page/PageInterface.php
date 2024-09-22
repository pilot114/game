<?php

namespace Game\UI\Page;

use PhpTui\Term\Event;
use PhpTui\Tui\Model\Display\Display;

interface PageInterface
{
    public function handle(Event $event): ?PageInterface;
    public function render(Display $display): void;
}
