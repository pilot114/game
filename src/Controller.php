<?php

namespace Game;

use Game\Page\HeroStartPage;
use Game\Page\PageInterface;
use Game\Page\MainPage;
use Game\Page\Stop;
use PhpTui\Term\Actions;
use PhpTui\Term\ClearType;
use PhpTui\Term\Event;
use PhpTui\Term\Event\CharKeyEvent;
use PhpTui\Term\Terminal;
use PhpTui\Tui\DisplayBuilder;
use PhpTui\Tui\Extension\Bdf\BdfExtension;
use PhpTui\Tui\Extension\ImageMagick\ImageMagickExtension;
use PhpTui\Tui\Model\Display\Display;

class Controller
{
    use GameProgress;

    private Terminal $terminal;
    private Display $display;
    private PageInterface $page;

    private bool $isRun = true;

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

        //$this->page = new MainPage();
        $this->page = new HeroStartPage();

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
        while ($this->isRun) {
            while (null !== $event = $this->terminal->events()->next()) {
                $page = $this->globalHandle($event) ?: $this->page->handle($event);
                if ($page !== null) {
                    if ($page instanceof Stop) {
                        $this->isRun = false;
                    }
                    $this->page = $page;
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

    private function globalHandle(Event $event): ?PageInterface
    {
        if ($event instanceof Event\CodedKeyEvent) {
            if ($event->code->name === 'Esc' && (!$this->page instanceof MainPage)) {
                return new MainPage();
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
