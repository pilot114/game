<?php

namespace Game;

use Game\Page\PageInterface;
use Game\Page\StartPage;
use PhpTui\Term\Actions;
use PhpTui\Term\ClearType;
use PhpTui\Term\Event;
use PhpTui\Term\Event\CharKeyEvent;
use PhpTui\Term\KeyModifiers;
use PhpTui\Term\Terminal;
use PhpTui\Tui\DisplayBuilder;
use PhpTui\Tui\Extension\Bdf\BdfExtension;
use PhpTui\Tui\Extension\ImageMagick\ImageMagickExtension;
use PhpTui\Tui\Model\Display\Display;

class Controller
{
    use GameProgress;

    private Terminal $terminal;
    private PageInterface $page;
    private Display $display;

    private bool $isRun = true;

    public function __construct(
        private UI     $ui,
        private Player $player,
        private World  $world,
    )
    {
        $this->terminal = Terminal::new();
        $this->page = new StartPage();
        $this->display = DisplayBuilder::default()
            ->addExtension(new ImageMagickExtension())
            ->addExtension(new BdfExtension())
            ->build();
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
        while ($this->isRun) {
            while (null !== $event = $this->terminal->events()->next()) {
                $this->globalHandle($event);
                $page = $this->page->handle($event);
                if ($page !== null) {
                    $this->page = $page;
                }
            }
            $this->page->render($this->display);
        }

        $this->terminal->disableRawMode();
        $this->terminal->execute(Actions::cursorShow());
        $this->terminal->execute(Actions::alternateScreenDisable());
        $this->terminal->execute(Actions::disableMouseCapture());

        return 0;
    }

    private function globalHandle(Event $event): void
    {
        // TODO: top widget for main menu
        if ($event instanceof CharKeyEvent) {
            if ($event->modifiers === KeyModifiers::NONE) {
                if ($event->char === 'q') {
                    $this->isRun = false;
                }
            }
        }
        return;

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
