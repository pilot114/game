<?php

namespace Game\UI\Page\Test;

use Game\UI\AbstractPage;
use Game\UI\PageEvent;
use Game\UI\PageEventType;
use PhpTui\Term\Event;
use PhpTui\Term\MouseEventKind;
use PhpTui\Term\Terminal;
use PhpTui\Tui\Display\Area;
use PhpTui\Tui\Display\Display;
use PhpTui\Tui\Extension\Core\Widget\BlockWidget;
use PhpTui\Tui\Extension\Core\Widget\Buffer\BufferContext;
use PhpTui\Tui\Extension\Core\Widget\BufferWidget;
use PhpTui\Tui\Position\Position;
use PhpTui\Tui\Text\Line;
use PhpTui\Tui\Text\Title;
use PhpTui\Tui\Widget\Borders;
use PhpTui\Tui\Widget\HorizontalAlignment;

class DragPage extends AbstractPage
{
    private ?Position $dragPosition = null;
    private bool $isDragging = false;
    private Position $elementPosition;

    public function __construct(
        protected Terminal $terminal,
        protected Display $display,
    ){
        parent::__construct($terminal, $display);
        $this->elementPosition = Position::at(10, 10);
    }

    public function draw(): void
    {
        $widget = BufferWidget::new(function (BufferContext $context): void {
            $elementArea = Area::fromScalars(
                $this->elementPosition->x,
                $this->elementPosition->y,
                20,
                10
            );

            if ($this->isDragging) {
                $this->elementPosition = $this->dragPosition;
                $title = Title::fromLine(Line::fromString('Dragging')->red()->onGreen());

                $elementArea = Area::fromScalars(
                    max($context->area->left(), min($this->dragPosition->x, max(0, $context->area->right() - 20))),
                    min(max($context->area->top(), $this->dragPosition->y), max(0, $context->area->bottom() - 10)),
                    20,
                    10
                );
            } else {
                $title = Title::fromLine(Line::fromString('Drag me'));
            }

            $block = BlockWidget::default()
                ->borders(Borders::ALL)
                ->titles(
                    $title,
                    Title::fromString($this->elementPosition->__toString())->horizontalAlignmnet(HorizontalAlignment::Right)
                );
            $context->draw($block, $elementArea);
        });
        $this->display->draw($widget);
    }

    public function handle(Event $event): ?PageEvent
    {
        if ($event instanceof Event\CharKeyEvent) {
            if ($event->char === 'q') {
                return $this->emitChangePageEvent(GridPage::class);
            }
        }
        if ($event instanceof Event\MouseEvent) {
            if ($event->kind === MouseEventKind::Drag) {
                $this->dragPosition = Position::at($event->column, $event->row);
                $elementArea = Area::fromScalars($this->elementPosition->x, $this->elementPosition->y, 20, 10);
                if ($elementArea->containsPosition($this->dragPosition)) {
                    $this->isDragging = true;
                }
            }
            if ($event->kind === MouseEventKind::Up && $this->dragPosition) {
                $this->dragPosition = null;
                $this->isDragging = false;
            }
        }
        if ($this->isDragging) {
            return new PageEvent(PageEventType::NeedDraw);
        }
        return null;
    }
}