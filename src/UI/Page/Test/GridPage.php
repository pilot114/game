<?php

namespace Game\UI\Page\Test;

use Game\UI\AbstractPage;
use Game\UI\PageEvent;
use Game\UI\PageEventType;
use Game\UI\PageInterface;
use PhpTui\Term\Event;
use PhpTui\Tui\Extension\Core\Widget\BlockWidget;
use PhpTui\Tui\Extension\Core\Widget\GridWidget;
use PhpTui\Tui\Layout\Constraint;
use PhpTui\Tui\Text\Title;
use PhpTui\Tui\Widget\Borders;
use PhpTui\Tui\Widget\BorderType;
use PhpTui\Tui\Widget\Direction;

class GridPage extends AbstractPage
{
    function draw(): void
    {
        $this->display->draw(
            GridWidget::default()
                ->direction(Direction::Horizontal)
                ->constraints(
                    Constraint::percentage(20),
                    Constraint::percentage(60),
                    Constraint::percentage(20),
                )
                ->widgets(
                    BlockWidget::default()
                        ->borders(Borders::ALL)
                        ->borderType(BorderType::Rounded)
                        ->titles(Title::fromString('1234567890')),
                    BlockWidget::default()
                        ->borders(Borders::ALL)
                        ->borderType(BorderType::Rounded)
                        ->titles(Title::fromString('1234567890')),
                    BlockWidget::default()
                        ->borders(Borders::ALL)
                        ->borderType(BorderType::Rounded)
                        ->titles(Title::fromString('1234567890')),
                )
        );
    }

    public function handle(Event $event): ?PageEvent
    {
        if ($event instanceof Event\CharKeyEvent) {
            if ($event->char === 'q') {
                return $this->emitChangePageEvent(DragPage::class);
            }
        }
        return null;
    }
}