<?php

namespace Game\UI;

use PhpTui\Term\Event;
use PhpTui\Term\Terminal;
use PhpTui\Tui\Display\Display;

abstract class AbstractPage implements PageInterface
{
    public function __construct(
        protected Terminal $terminal,
        protected Display  $display,
    ){
    }

    public function handle(Event $event): ?PageEvent
    {
        return null;
    }

    abstract public function draw(): void;

    protected function emitChangePageEvent(string $pageName): PageEvent
    {
        $page = new $pageName($this->terminal, $this->display);
        return new PageEvent(PageEventType::ChangePage, $page);
    }

    protected function isClick(Event $event): bool
    {
        if (!$event instanceof Event\MouseEvent) {
            return false;
        }
        return $event->button->name === 'Left' && $event->kind->name === 'Down';
    }
}