<?php

namespace Game\UI\Page;

use Game\UI\AbstractPage;
use Game\UI\PageEvent;
use Game\UI\PageEventType;
use PhpTui\Term\Event;
use PhpTui\Tui\Extension\Core\Widget\BlockWidget;
use PhpTui\Tui\Extension\Core\Widget\GridWidget;
use PhpTui\Tui\Extension\Core\Widget\List\ListItem;
use PhpTui\Tui\Extension\Core\Widget\List\ListState;
use PhpTui\Tui\Extension\Core\Widget\ListWidget;
use PhpTui\Tui\Extension\Core\Widget\ParagraphWidget;
use PhpTui\Tui\Layout\Constraint;
use PhpTui\Tui\Text\Text;
use PhpTui\Tui\Widget\Borders;
use PhpTui\Tui\Widget\BorderType;
use PhpTui\Tui\Widget\Direction;
use PhpTui\Tui\Widget\HorizontalAlignment;

class MainPage extends AbstractPage
{
    private int $itemIndex = 0;
    private int $buildNumber = 2;

    public function handle(Event $event): ?PageEvent
    {
        if ($event instanceof Event\MouseEvent) {
            return null;
        }
        if ($event instanceof Event\CodedKeyEvent) {
            if ($event->code->name === 'Up') {
                $this->itemIndex === 0 ? $this->itemIndex = 4 : $this->itemIndex--;
                return new PageEvent(PageEventType::NeedDraw);
            }
            if ($event->code->name === 'Down') {
                $this->itemIndex === 4 ? $this->itemIndex = 0 : $this->itemIndex++;
                return new PageEvent(PageEventType::NeedDraw);
            }
            if ($event->code->name === 'Enter') {
                return match ($this->itemIndex) {
                    0 => $this->emitChangePageEvent(HeroStartPage::class),
                    1, 2 => null,
                    3 => $this->emitChangePageEvent(SettingsPage::class),
                    4 => new PageEvent(PageEventType::Stop),
                };
            }
        }
        return null;
    }

    public function draw(): void
    {
        $this->display->draw(
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
                            ParagraphWidget::fromString("
┏┳┓┓     ┓         ┏┓       
 ┃ ┣┓┏┓  ┃ ┏┓┏┓┏┓  ┗┓╋┏┓┏┓┓┏
 ┻ ┛┗┗   ┗┛┗┛┛┗┗┫  ┗┛┗┗┛┛ ┗┫
                ┛          ┛
build $this->buildNumber by 0x600dc0de
")->alignment(HorizontalAlignment::Center),
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
                                        ListItem::new(Text::fromString(`img2sixel resources/images/doomgay2.jpg`)),
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
    // TODO: background
}
