<?php

namespace Game\Page;

use PhpTui\Term\Event;
use PhpTui\Term\MouseButton;
use PhpTui\Tui\DisplayBuilder;
use PhpTui\Tui\Extension\Bdf\BdfExtension;
use PhpTui\Tui\Extension\Core\Widget\Block\Padding;
use PhpTui\Tui\Extension\Core\Widget\BlockWidget;
use PhpTui\Tui\Extension\Core\Widget\GridWidget;
use PhpTui\Tui\Extension\Core\Widget\List\ListItem;
use PhpTui\Tui\Extension\Core\Widget\List\ListState;
use PhpTui\Tui\Extension\Core\Widget\ListWidget;
use PhpTui\Tui\Extension\Core\Widget\ParagraphWidget;
use PhpTui\Tui\Extension\ImageMagick\ImageMagickExtension;
use PhpTui\Tui\Extension\ImageMagick\Widget\ImageWidget;
use PhpTui\Tui\Model\Direction;
use PhpTui\Tui\Model\Display\Display;
use PhpTui\Tui\Model\HorizontalAlignment;
use PhpTui\Tui\Model\Layout\Constraint;
use PhpTui\Tui\Model\Style;
use PhpTui\Tui\Model\Text\Text;
use PhpTui\Tui\Model\Text\Title;
use PhpTui\Tui\Model\Widget\Borders;
use PhpTui\Tui\Model\Widget\BorderType;

class HeroStartPage implements PageInterface
{
    private string $race = 'human';
    private string $class = 'warrior';
    private string $gender = 'men';

    private string $description = '';

    public function handle(Event $event): ?PageInterface
    {
        $this->description = print_r($event, true);

        if ($event instanceof Event\MouseEvent) {
            if ($event->button->name !== 'Left' || $event->kind->name !== 'Down') {
                return null;
            }
            $areas = [
                'race_human' => [1,1,34,1],
                'race_lizzard' => [1,2,34,2],
                'race_gnome' => [1,3,34,3],
                'class_warrior' => [1,6,34,6],
                'class_wizard' => [1,7,34,7],
                'gender_men' => [1,10,34,10],
                'gender_women' => [1,11,34,11],
                'start' => [109,36,143,36],
            ];
            foreach ($areas as $type => $area) {
                $inArea = $event->column >= $area[0] && $event->column <= $area[2] && $event->row >= $area[1] && $event->row <= $area[3];
                if ($inArea) {
                    if ($type === 'start') {
                        return new GamePage();
                    }
                    [$key, $value] = explode('_', $type);
                    $this->$key = $value;
                }
            }
        }
        return null;
    }

    private function getPortraitPath(): string
    {
        return __DIR__ . "/../../resources/images/hero/{$this->race}_{$this->gender}_{$this->class}.webp";
    }

    private function getRaceIndex(): int
    {
        return match($this->race) {
            'human' => 0,
            'lizzard' => 1,
            'gnome' => 2,
        };
    }

    private function getClassIndex(): int
    {
        return match($this->class) {
            'warrior' => 0,
            'wizard' => 1,
        };
    }

    private function getGenderIndex(): int
    {
        return match($this->gender) {
            'men' => 0,
            'women' => 1,
        };
    }

    public function render(Display $display): void
    {
        $display->draw(
            GridWidget::default()
            ->direction(Direction::Horizontal)
            ->constraints(
                Constraint::percentage(25),
                Constraint::percentage(50),
                Constraint::percentage(25),
            )
            ->widgets(
                GridWidget::default()
                ->direction(Direction::Vertical)
                ->constraints(
                    Constraint::max(5),
                    Constraint::max(4),
                    Constraint::max(4),
                    Constraint::max(7),
                    Constraint::max(50),
                )
                ->widgets(
                    BlockWidget::default()
                        ->borders(Borders::ALL)
                        ->borderType(BorderType::Rounded)
                        ->titles(Title::fromString('Раса'))
                    ->widget(
                        ListWidget::default()
                            ->highlightSymbol('🡆')
                            ->state(new ListState(0, $this->getRaceIndex()))
                            ->items(
                                ListItem::new(Text::fromString(' Человек')),
                                ListItem::new(Text::fromString(' Ящер')),
                                ListItem::new(Text::fromString(' Гном')),
                            )
                    ),
                    BlockWidget::default()
                        ->borders(Borders::ALL)
                        ->borderType(BorderType::Rounded)
                        ->titles(Title::fromString('Класс'))
                        ->widget(
                            ListWidget::default()
                                ->highlightSymbol('🡆')
                                ->state(new ListState(0, $this->getClassIndex()))
                                ->items(
                                    ListItem::new(Text::fromString(' Воин')),
                                    ListItem::new(Text::fromString(' Маг')),
                                )
                        ),
                    BlockWidget::default()
                        ->borders(Borders::ALL)
                        ->borderType(BorderType::Rounded)
                        ->titles(Title::fromString('Пол'))
                        ->widget(
                            ListWidget::default()
                                ->highlightSymbol('🡆')
                                ->state(new ListState(0, $this->getGenderIndex()))
                                ->items(
                                    ListItem::new(Text::fromString(' Мужской')),
                                    ListItem::new(Text::fromString(' Женский')),
                                )
                        ),
                    BlockWidget::default()
                        ->borders(Borders::ALL)
                        ->borderType(BorderType::Rounded)
                        ->titles(Title::fromString('Особенность'))
                        ->widget(
                            ParagraphWidget::fromString('...')
                        ),
                    BlockWidget::default()
                        ->borders(Borders::ALL)
                        ->borderType(BorderType::Rounded)
                        ->titles(Title::fromString('Параметры'))
                ),
                BlockWidget::default()
                    ->borders(Borders::ALL)
                    ->borderType(BorderType::Rounded)
                    ->titles(Title::fromString('Портрет'))
                    ->widget(
//                        ParagraphWidget::fromString($this->getPortraitPath())
                        ImageWidget::fromPath($this->getPortraitPath()),
                    ),
                GridWidget::default()
                ->direction(Direction::Vertical)
                ->constraints(
                    Constraint::percentage(90),
                    Constraint::max(3),
                )
                ->widgets(
                    BlockWidget::default()
                        ->borders(Borders::ALL)
                        ->borderType(BorderType::Rounded)
                        ->titles(Title::fromString('Описание'))
                        ->widget(
                            ParagraphWidget::fromString($this->description)
                        ),
                    BlockWidget::default()->style(
                        Style::default()
                    )
                    ->borders(Borders::ALL)->borderType(BorderType::Rounded)
                    ->widget(
                        ParagraphWidget::fromString('Сгенерировать мир и начать игру')->alignment(HorizontalAlignment::Center)
                    )
                ),
            )
        );
    }
}
