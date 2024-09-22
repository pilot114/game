<?php

declare(strict_types=1);

include __DIR__ . '/../vendor/autoload.php';

use PhpTui\Term\Actions;
use PhpTui\Term\ClearType;
use PhpTui\Term\Event;
use PhpTui\Term\MouseEventKind;
use PhpTui\Term\Terminal;
use PhpTui\Tui\Display\Area;
use PhpTui\Tui\Display\Display;
use PhpTui\Tui\DisplayBuilder;
use PhpTui\Tui\Extension\Bdf\BdfExtension;
use PhpTui\Tui\Extension\Core\Widget\BlockWidget;
use PhpTui\Tui\Extension\Core\Widget\Buffer\BufferContext;
use PhpTui\Tui\Extension\Core\Widget\BufferWidget;
use PhpTui\Tui\Extension\Core\Widget\GridWidget;
use PhpTui\Tui\Extension\Core\Widget\ParagraphWidget;
use PhpTui\Tui\Extension\ImageMagick\ImageMagickExtension;
use PhpTui\Tui\Extension\ImageMagick\Widget\ImageWidget;
use PhpTui\Tui\Layout\Constraint;
use PhpTui\Tui\Position\Position;
use PhpTui\Tui\Style\Style;
use PhpTui\Tui\Text\Line;
use PhpTui\Tui\Text\Title;
use PhpTui\Tui\Widget\Borders;
use PhpTui\Tui\Widget\BorderType;
use PhpTui\Tui\Widget\Direction;
use PhpTui\Tui\Widget\HorizontalAlignment;

abstract class Demo
{
    private bool $isRunning = true;
    protected bool $isNeedDrawDebug = false;
    protected bool $isNeedDraw = true;

    private Terminal $terminal;
    protected Display $display;

    private bool $isDebug = false;
    private ?Event $lastEvent = null;

    public function __construct()
    {
        $this->terminal = Terminal::new();
        $this->display = DisplayBuilder::default()
            ->addExtension(new ImageMagickExtension())
            ->addExtension(new BdfExtension())
            ->build();
    }

    public function run(): void
    {
        try {
            $this->terminal->execute(Actions::cursorHide());
            $this->terminal->execute(Actions::alternateScreenEnable());
            $this->terminal->execute(Actions::enableMouseCapture());
            $this->terminal->enableRawMode();

            while ($this->isRunning) {
                while (null !== $event = $this->terminal->events()->next()) {
                    $this->lastEvent = $event;
                    $this->handleBase($event);
                    $this->handle($event);
                }
                if ($this->isNeedDraw) {
                    $this->draw();
                    $this->isNeedDraw = false;
                }
                if ($this->isNeedDrawDebug) {
                    $this->drawBase();
                    $this->isNeedDrawDebug = false;
                }
                usleep(10_000);
            }
        } finally {
            $this->terminal->disableRawMode();
            $this->terminal->execute(Actions::disableMouseCapture());
            $this->terminal->execute(Actions::alternateScreenDisable());
            $this->terminal->execute(Actions::cursorShow());
            $this->terminal->execute(Actions::clear(ClearType::All));
        }
    }

    abstract public function handle(Event $event): void;
    abstract public function draw(): void;

    protected function handleBase(Event $event): void
    {
        if ($event instanceof Event\TerminalResizedEvent) {
            $this->isNeedDraw = true;
        }
        if ($event instanceof Event\CodedKeyEvent) {
            if ($event->code->name === 'Esc') {
                $this->isRunning = false;
            }
        }
        if ($event instanceof Event\CharKeyEvent) {
            if ($event->char === '`') {
                $this->isDebug = !$this->isDebug;
                if (!$this->isDebug) {
                    $this->display->clear();
                    $this->isNeedDraw = true;
                    $this->isNeedDrawDebug = false;
                }
            }
        }
        if ($this->isDebug) {
            $this->isNeedDrawDebug = true;
        }
    }

    private function drawBase(): void
    {
        $display = DisplayBuilder::default()->fixed(0, 20, 50, 20)->build();
        $display->draw(
            BlockWidget::default()->borders(Borders::ALL)->titles(Title::fromString('debug events'))
                ->style(Style::default()->yellow())
                ->widget(
                    ParagraphWidget::fromString(print_r($this->lastEvent, true))->style(Style::default()->yellow())
                )
        );
    }
}

class GridTest extends Demo
{
    function draw(): void
    {
        $width = $this->display->viewportArea()->width;
        $sides = (int)round(($width - 80) / 2);

        $this->display->draw(
            GridWidget::default()
                ->direction(Direction::Horizontal)
                ->constraints(
                    Constraint::length($sides),
                    Constraint::length(80),
                    Constraint::length($sides),
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

    function handle(Event $event): void
    {
        // TODO: Implement handle() method.
    }
}

class DragTest extends Demo
{
    private ?Position $dragPosition = null;
    private bool $isDragging = false;
    private Position $elementPosition;

    public function __construct()
    {
        parent::__construct();
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

    function handle(Event $event): void
    {
        if ($this->isDragging) {
            $this->isNeedDraw = true;
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
    }
}

$demo = new DragTest();
$demo->run();


