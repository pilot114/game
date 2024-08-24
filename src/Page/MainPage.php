<?php

namespace Game\Page;

use PhpTui\Term\Event;
use PhpTui\Tui\Extension\Core\Widget\BlockWidget;
use PhpTui\Tui\Extension\Core\Widget\GridWidget;
use PhpTui\Tui\Extension\Core\Widget\List\ListItem;
use PhpTui\Tui\Extension\Core\Widget\List\ListState;
use PhpTui\Tui\Extension\Core\Widget\ListWidget;
use PhpTui\Tui\Extension\Core\Widget\ParagraphWidget;
use PhpTui\Tui\Model\Direction;
use PhpTui\Tui\Model\Display\Display;
use PhpTui\Tui\Model\HorizontalAlignment;
use PhpTui\Tui\Model\Layout\Constraint;
use PhpTui\Tui\Model\Text\Text;
use PhpTui\Tui\Model\Widget\Borders;
use PhpTui\Tui\Model\Widget\BorderType;

class MainPage implements PageInterface
{
    private int $itemIndex = 0;

    public function handle(Event $event): ?PageInterface
    {
        if ($event instanceof Event\MouseEvent) {
            return null;
        }
        if ($event instanceof Event\CodedKeyEvent) {
            if ($event->code->name === 'Up') {
                $this->itemIndex === 0 ? $this->itemIndex = 4 : $this->itemIndex--;
            }
            if ($event->code->name === 'Down') {
                $this->itemIndex === 4 ? $this->itemIndex = 0 : $this->itemIndex++;
            }
            if ($event->code->name === 'Enter') {
                return match ($this->itemIndex) {
                    0 => new HeroStartPage(),
                    1 => null,
                    2 => null,
                    3 => new SettingsPage(),
                    4 => new Stop(),
                    default => null,
                };
            }
        }
        return null;
    }

    public function render(Display $display): void
    {
        $display->draw(
            GridWidget::default()
            ->direction(Direction::Horizontal)
            ->constraints(
                Constraint::percentage(35),
                Constraint::percentage(30),
                Constraint::percentage(35),
            )
            ->widgets(
                BlockWidget::default()->borders(Borders::NONE),
                GridWidget::default()
                ->direction(Direction::Vertical)
                ->constraints(
                    Constraint::percentage(20),
                    Constraint::percentage(45),
                    Constraint::percentage(25),
                )
                ->widgets(
                    BlockWidget::default()->borders(Borders::NONE),
                    BlockWidget::default()->borders(Borders::ALL)->borderType(BorderType::Rounded)->widget(
                        GridWidget::default()
                        ->constraints(
                            Constraint::min(8),
                            Constraint::percentage(100),
                        )
                        ->widgets(
                            ParagraphWidget::fromString('
┏┳┓┓     ┓         ┏┓       
 ┃ ┣┓┏┓  ┃ ┏┓┏┓┏┓  ┗┓╋┏┓┏┓┓┏
 ┻ ┛┗┗   ┗┛┗┛┛┗┗┫  ┗┛┗┗┛┛ ┗┫
                ┛          ┛
build 1 by 0x600dc0de
')->alignment(HorizontalAlignment::Center),
                            GridWidget::default()
                            ->direction(Direction::Horizontal)
                            ->constraints(
                                Constraint::percentage(35),
                                Constraint::percentage(70),
                            )
                            ->widgets(
                                BlockWidget::default()->borders(Borders::NONE),
                                ListWidget::default()
                                    ->highlightSymbol('🡆')
                                    ->state(new ListState(0, $this->itemIndex))
                                    ->items(
                                        ListItem::new(Text::fromString(' Новая игра')),
                                        ListItem::new(Text::fromString(' Сохранить')),
                                        ListItem::new(Text::fromString(' Загрузить')),
                                        ListItem::new(Text::fromString(' Настройки')),
                                        ListItem::new(Text::fromString(' Выход')),
                                    )
                            )
                        )
                    ),
                    BlockWidget::default()->borders(Borders::NONE),
                ),
                BlockWidget::default()->borders(Borders::NONE),
            )
        );
    }

    // TODO: cache render, for background
    //        new ImageWidget(path: __DIR__ . '/../resources/oblivion.jpg')
}
