<?php

namespace Game\UI;

use Game\UI\Page\MainPage;
use Game\UI\Page\SettingsPage;
use PhpTui\Term\Actions;
use PhpTui\Term\ClearType;
use PhpTui\Term\Event;
use PhpTui\Term\Terminal;
use PhpTui\Tui\Display\Display;
use PhpTui\Tui\DisplayBuilder;
use PhpTui\Tui\Extension\Bdf\BdfExtension;
use PhpTui\Tui\Extension\Core\Widget\BlockWidget;
use PhpTui\Tui\Extension\Core\Widget\ParagraphWidget;
use PhpTui\Tui\Extension\ImageMagick\ImageMagickExtension;
use PhpTui\Tui\Style\Style;
use PhpTui\Tui\Text\Title;
use PhpTui\Tui\Widget\Borders;

final class PageController
{
    private Terminal $terminal;
    private Display $display;
    private PageInterface $activePage;

    private bool $isRunning = true;
    private ?Event $lastEvent = null;
    private bool $isDebug = false;
    private bool $isNeedDrawDebug = false;
    private bool $isNeedDraw = true;

    public function __construct(string $startPage)
    {
        $this->terminal = Terminal::new();
        $this->display = DisplayBuilder::default()
            ->addExtension(new ImageMagickExtension())
            ->addExtension(new BdfExtension())
            ->build();

        $this->activePage = new $startPage($this->terminal, $this->display);
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
                    $pageEvent = $this->handleBase($event);
                    if ($pageEvent !== null) {
                        $this->handlePageEvent($pageEvent);
                    }
                    $pageEvent = $this->activePage->handle($event);
                    if ($pageEvent !== null) {
                        $this->handlePageEvent($pageEvent);
                    }
                }
                if ($this->isNeedDraw) {
                    $this->activePage->draw();
                    $this->isNeedDraw = false;
                }
                if ($this->isNeedDrawDebug) {
                    $this->drawDebug();
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


    protected function handlePageEvent(PageEvent $event): void
    {
        if ($event->eventType === PageEventType::ChangePage) {
            $this->activePage = $event->data;
            $this->isNeedDraw = true;
        }
        if ($event->eventType === PageEventType::NeedDraw) {
            $this->isNeedDraw = true;
        }
        if ($event->eventType === PageEventType::Stop) {
            $this->isRunning = false;
        }
    }

    protected function handleBase(Event $event): ?PageEvent
    {
        if ($event instanceof Event\TerminalResizedEvent) {
            $this->isNeedDraw = true;
        }
        if ($event instanceof Event\CodedKeyEvent) {
            if ($event->code->name === 'Esc') {
                return new PageEvent(
                    PageEventType::ChangePage,
                    new MainPage($this->terminal, $this->display)
                );
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
        return null;
    }

    private function drawDebug(): void
    {
        $message = print_r($this->lastEvent, true);
        $message .= $this->activePage::class . "\n";

        $display = DisplayBuilder::default()->fixed(0, 20, 50, 20)->build();
        $display->draw(
            BlockWidget::default()->borders(Borders::ALL)
                ->titles(Title::fromString('debug events'))
                ->style(Style::default()->yellow())
                ->widget(
                    ParagraphWidget::fromString($message)->style(Style::default()->yellow())
                )
        );
    }
}