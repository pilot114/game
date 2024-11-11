<?php

namespace Game\UI\Page;

use Game\UI\AbstractPage;
use Game\UI\PageEvent;
use Game\UI\PageEventType;
use PhpTui\Term\Event;
use PhpTui\Term\Terminal;
use PhpTui\Tui\Display\Display;
use PhpTui\Tui\Extension\Core\Widget\BlockWidget;
use PhpTui\Tui\Extension\Core\Widget\GridWidget;
use PhpTui\Tui\Extension\Core\Widget\List\ListItem;
use PhpTui\Tui\Extension\Core\Widget\List\ListState;
use PhpTui\Tui\Extension\Core\Widget\ListWidget;
use PhpTui\Tui\Extension\Core\Widget\ParagraphWidget;
use PhpTui\Tui\Layout\Constraint;
use PhpTui\Tui\Style\Style;
use PhpTui\Tui\Text\Text;
use PhpTui\Tui\Widget\Borders;
use PhpTui\Tui\Widget\BorderType;
use PhpTui\Tui\Widget\Direction;
use PhpTui\Tui\Widget\HorizontalAlignment;

class SettingsPage extends AbstractPage
{
    private array $colours = [];
    private int $colourIndex = 3;

    public function __construct(
        protected Terminal $terminal,
        protected Display  $display,
    ){
        parent::__construct($this->terminal, $this->display);
        $this->colours = [
            Style::default()->yellow(),
            Style::default()->green(),
            Style::default()->gray(),
            Style::default()->blue(),
        ];
    }

    public function handle(Event $event): ?PageEvent
    {
        if ($this->isClick($event)) {
            // TODO: inArea check
            if ($event->column === 110 && $event->row === 14) {
                $this->colourIndex === 0 ? $this->colourIndex = 3 : $this->colourIndex--;
                return new PageEvent(PageEventType::NeedDraw);
            }
            if ($event->column === 119 && $event->row === 14) {
                $this->colourIndex === 3 ? $this->colourIndex = 0 : $this->colourIndex++;
                return new PageEvent(PageEventType::NeedDraw);
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

/*

. -------------------------------------------------------------------.
| [Esc] [F1][F2][F3][F4][F5][F6][F7][F8][F9][F0][F10][F11][F12] o o o|
|                                                                    |
| [`][1][2][3][4][5][6][7][8][9][0][-][=][_<_] [I][H][U] [N][/][*][-]|
| [|-][Q][W][E][R][T][Y][U][I][O][P][{][}] | | [D][E][D] [7][8][9]|+||
| [CAP][A][S][D][F][G][H][J][K][L][;]['][#]|_|           [4][5][6]|_||
| [^][\][Z][X][C][V][B][N][M][,][.][/] [__^__]    [^]    [1][2][3]| ||
| [c]   [a][________________________][a]   [c] [<][V][>] [ 0  ][.]|_||
`--------------------------------------------------------------------'

 */
