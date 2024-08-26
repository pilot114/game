<?php

namespace Game\Page;

use PhpTui\Term\Event;
use PhpTui\Tui\DisplayBuilder;
use PhpTui\Tui\Extension\Bdf\BdfExtension;
use PhpTui\Tui\Extension\Core\Widget\BlockWidget;
use PhpTui\Tui\Extension\Core\Widget\GridWidget;
use PhpTui\Tui\Extension\Core\Widget\List\ListItem;
use PhpTui\Tui\Extension\Core\Widget\List\ListState;
use PhpTui\Tui\Extension\Core\Widget\ListWidget;
use PhpTui\Tui\Extension\Core\Widget\ParagraphWidget;
use PhpTui\Tui\Extension\ImageMagick\ImageMagickExtension;
use PhpTui\Tui\Extension\ImageMagick\Widget\ImageWidget;
use PhpTui\Tui\Model\Color;
use PhpTui\Tui\Model\Color\AnsiColor;
use PhpTui\Tui\Model\Direction;
use PhpTui\Tui\Model\Display\Display;
use PhpTui\Tui\Model\HorizontalAlignment;
use PhpTui\Tui\Model\Layout\Constraint;
use PhpTui\Tui\Model\Style;
use PhpTui\Tui\Model\Text\Text;
use PhpTui\Tui\Model\Widget\Borders;
use PhpTui\Tui\Model\Widget\BorderType;

class SettingsPage implements PageInterface
{
    private array $colours = [];
    private int $colourIndex = 3;

    public function __construct()
    {
        $this->colours = [
            Style::default()->yellow(),
            Style::default()->green(),
            Style::default()->gray(),
            Style::default()->blue(),
        ];
    }

    public function handle(Event $event): ?PageInterface
    {
        if ($event instanceof Event\MouseEvent) {
            if ($event->button->name !== 'Left' || $event->kind->name !== 'Down') {
                return null;
            }
            if ($event->column === 76 && $event->row === 11) {
                $this->colourIndex === 0 ? $this->colourIndex = 3 : $this->colourIndex--;
            }
            if ($event->column === 85 && $event->row === 11) {
                $this->colourIndex === 3 ? $this->colourIndex = 0 : $this->colourIndex++;
            }
        }
        return null;
    }

    private function getStyle(): Style
    {
        return $this->colours[$this->colourIndex];
    }

    private function getColorName(): string
    {
        $name = (string)$this->colours[$this->colourIndex]->fg->debugName();
        return match($name) {
            'Yellow' => 'Жёлтый',
            'Green' => 'Зелёный',
            'Gray' => 'Серый',
            'Blue' => 'Синий',
        };
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
                            BlockWidget::default()->borders(Borders::ALL)->borderType(BorderType::Rounded)
                                ->style($this->getStyle())
                                ->widget(
                                GridWidget::default()
                                    ->constraints(
                                        Constraint::percentage(15),
                                        Constraint::percentage(25),
                                        Constraint::percentage(25),
                                        Constraint::percentage(25),
                                    )
                                    ->widgets(
                                        ParagraphWidget::fromString('Настройки')->alignment(HorizontalAlignment::Center)->style($this->getStyle()),
                                        GridWidget::default()
                                            ->direction(Direction::Horizontal)
                                            ->constraints(
                                                Constraint::percentage(15),
                                                Constraint::percentage(40),
                                                Constraint::percentage(5),
                                                Constraint::percentage(50),
                                            )
                                            ->widgets(
                                                BlockWidget::default()->borders(Borders::NONE),
                                                ParagraphWidget::fromString('Цвет интерфейса')->style($this->getStyle()),
                                                BlockWidget::default()->borders(Borders::NONE),
                                                GridWidget::default()
                                                    ->direction(Direction::Horizontal)
                                                    ->constraints(
                                                        Constraint::min(2),
                                                        Constraint::max(7),
                                                        Constraint::min(2),
                                                    )
                                                    ->widgets(
                                                        ParagraphWidget::fromString('⮘')->style($this->getStyle()),
                                                        ParagraphWidget::fromString($this->getColorName())->style($this->getStyle()),
                                                        ParagraphWidget::fromString('⮚')->style($this->getStyle()),
                                                    )
                                            ),
                                        GridWidget::default()
                                            ->direction(Direction::Horizontal)
                                            ->constraints(
                                                Constraint::percentage(15),
                                                Constraint::percentage(40),
                                                Constraint::percentage(5),
                                                Constraint::percentage(50),
                                            )
                                            ->widgets(
                                                BlockWidget::default()->borders(Borders::NONE),
                                                ParagraphWidget::fromString('Сложность')->style($this->getStyle()),
                                                BlockWidget::default()->borders(Borders::NONE),
                                                ListWidget::default()
                                                    ->highlightSymbol('🡆')
                                                    ->state(new ListState(0, 0))
                                                    ->items(
                                                        ListItem::new(Text::fromString(' Легко'))->style($this->getStyle()),
                                                        ListItem::new(Text::fromString(' Сложно'))->style($this->getStyle()),
                                                        ListItem::new(Text::fromString(' Хардкор'))->style(Style::default()->red()),
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
}
