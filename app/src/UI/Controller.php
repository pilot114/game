<?php

namespace Game\UI;

use Game\Audio;
use Game\GameProgress;
use Game\Player;
use Game\UI\Page\HeroStartPage;
use Game\UI\Page\MainPage;
use Game\UI\Page\PageInterface;
use Game\UI\Page\Stop;
use Game\World;
use PhpTui\Term\Actions;
use PhpTui\Term\ClearType;
use PhpTui\Term\Event;
use PhpTui\Term\Event\CharKeyEvent;
use PhpTui\Term\Terminal;
use PhpTui\Tui\DisplayBuilder;
use PhpTui\Tui\Extension\Bdf\BdfExtension;
use PhpTui\Tui\Extension\Core\Widget\BlockWidget;
use PhpTui\Tui\Extension\Core\Widget\ParagraphWidget;
use PhpTui\Tui\Extension\ImageMagick\ImageMagickExtension;
use PhpTui\Tui\Model\Style;
use PhpTui\Tui\Model\Text\Title;
use PhpTui\Tui\Model\Widget\Borders;

class Controller
{
    use GameProgress;

    private Terminal $terminal;
    private Display $display;
    private PageInterface $page;

    private bool $isDebug = false;
    private Event $lastEvent;

    public function __construct(
        private UI     $ui,
        private Player $player,
        private World  $world,
    )
    {
        $this->terminal = Terminal::new();
        $this->display = DisplayBuilder::default()
            ->addExtension(new ImageMagickExtension())
            ->addExtension(new BdfExtension())
            ->build();

//        $this->page = new MainPage();

          $this->page = new HeroStartPage();

        // for debug 1 frame
  //      $this->page->render($this->display);
//        exit;

        Audio::startMusic('luma_dream_machine.wav');
    }

    public function handleTerminal(): int
    {
        try {
            $this->terminal->execute(Actions::cursorHide());
            $this->terminal->execute(Actions::alternateScreenEnable());
            $this->terminal->execute(Actions::enableMouseCapture());
            $this->terminal->enableRawMode();

            return $this->mainLoop();
        } catch (\Throwable $err) {
            $this->terminal->disableRawMode();
            $this->terminal->execute(Actions::disableMouseCapture());
            $this->terminal->execute(Actions::alternateScreenDisable());
            $this->terminal->execute(Actions::cursorShow());
            $this->terminal->execute(Actions::clear(ClearType::All));

            throw $err;
        }
    }

    private function mainLoop(): int
    {
        while (true) {
            while (null !== $event = $this->terminal->events()->next()) {
                $this->lastEvent = $event;
                $page = $this->globalHandle($event) ?? $this->page->handle($event);
                if ($page !== null) {
                    if ($page instanceof Stop) {
                        break 2;
                    }
                    $this->page = $page;
                }
                if ($this->isDebug) {
                    $this->debugPanel();
                }
            }
            $this->page->render($this->display);
        }

        $this->terminal->disableRawMode();
        $this->terminal->execute(Actions::cursorShow());
        $this->terminal->execute(Actions::alternateScreenDisable());
        $this->terminal->execute(Actions::disableMouseCapture());

        Audio::stopMusic('luma_dream_machine.wav');

        return 0;
    }

    private function debugPanel(): void
    {
        $display = DisplayBuilder::default()->fixed(0, 20, 50, 20)->build();
        BlockWidget::default()->borders(Borders::NONE);
        $display->draw(
            BlockWidget::default()->borders(Borders::ALL)->titles(Title::fromString('debug events'))
                ->style(Style::default()->yellow())
                ->widget(
                    ParagraphWidget::fromString(print_r($this->lastEvent, true))->style(Style::default()->yellow())
                )
        );
    }

    private function globalHandle(Event $event): ?PageInterface
    {
        if ($event instanceof Event\CodedKeyEvent) {
            if ($event->code->name === 'Esc' && (!$this->page instanceof MainPage)) {
                return new MainPage();
            }
        }
        if ($event instanceof Event\CharKeyEvent) {
            if ($event->char === '`') {
                $this->isDebug = !$this->isDebug;
                $this->display->clear();
            }
        }
        return null;

        // TODO: buffer mode for input text

        if ($event instanceof CharKeyEvent) {
            /*
            $result = match ($event->char) {
                'l' => $this->world->look(),
                'm' => $this->world->move(),
                't' => $this->world->talkToNpc(),
                'take' => $this->world->takeItem(),
                'i' => $this->player->showInventory(),
                'd' => $this->player->dropItem(),
                'j' => $this->player->showQuests(),
                's' => $this->saveGame(),
                'load' => $this->loadGame(),
                default => $this->ui->output("Неизвестная команда\n"),
            };
            */
        }

        /*
        $this->world->display();
        $command = $this->ui->input("Команда: ");
        $this->processCommand($command);
        */
        /*
        $this->player->createCharacter();
        $this->world->generateWorld();
        */
    }
}
