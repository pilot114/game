<?php

namespace Game\UI\Page;

use PhpTui\Term\Event;
use PhpTui\Tui\Extension\Core\Widget\ParagraphWidget;
use PhpTui\Tui\Model\Direction;
use PhpTui\Tui\Model\Display\Display;
use PhpTui\Tui\Model\HorizontalAlignment;
use PhpTui\Tui\Model\Layout\Constraint;
use PhpTui\Tui\Model\Text\Text;
use PhpTui\Tui\Model\Widget\Borders;
use PhpTui\Tui\Model\Widget\BorderType;

class GamePage implements PageInterface
{
    public function __construct(
//        private World  $world,
    ) {
//        $world->generateWorld();
    }

    public function handle(Event $event): ?PageInterface
    {
        return null;
    }

    public function render(Display $display): void
    {
        $display->draw(
            ParagraphWidget::fromString('game'),
        );
    }
}
