<?php

namespace Game\Page;

use PhpTui\Term\Event;
use PhpTui\Tui\Model\Display\Display;

class Stop implements PageInterface
{
    public function handle(Event $event): ?PageInterface
    {
        return null;
    }

    public function render(Display $display): void
    {
    }
}
