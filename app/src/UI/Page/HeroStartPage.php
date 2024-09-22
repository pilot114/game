<?php

namespace Game\UI\Page;

use PhpTui\Term\Event;
use PhpTui\Tui\Extension\Core\Widget\BlockWidget;
use PhpTui\Tui\Extension\Core\Widget\GridWidget;
use PhpTui\Tui\Extension\Core\Widget\List\ListItem;
use PhpTui\Tui\Extension\Core\Widget\List\ListState;
use PhpTui\Tui\Extension\Core\Widget\ListWidget;
use PhpTui\Tui\Extension\Core\Widget\ParagraphWidget;
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

    private array $specialAbilities = [
        'Мастер торга' => 'Раз в сутки можно убедить торговца продать товар по себестоймости',
        'Паучье чутьё' => 'Вы получаете предупреждение и возможность убежать, если вступаете в бой сильно превосходящим вас противником',
        'Железный желудок' => 'Испорченная и отравленная еда не даёт негативных эффектов',
        'Эхо прошлого' => 'Вы можете видеть и слышать призраков',
        'Катализатор' => 'Вы притягиваете к себе редкие и случайные события, которые могут быть как полезными, так и опасными',
    ];
    private int $specialAbilityIndex = 0;

    private string $description = '<текстовое описание персонажа, собираемое по входным параметрам>';

    public function __construct()
    {
        $this->specialAbilityIndex = rand(0, 4);
    }

    private function getAbilityName(): string
    {
        return array_keys($this->specialAbilities)[$this->specialAbilityIndex];
    }
    private function getAbilityDescription(): string
    {
        return $this->specialAbilities[$this->getAbilityName()];
    }

    public function handle(Event $event): ?PageInterface
    {
        if ($event instanceof Event\MouseEvent) {
            if ($event->button->name !== 'Left' || $event->kind->name !== 'Down') {
                return null;
            }
            if ($event->column === 1 && $event->row === 14) {
                $this->specialAbilityIndex === 0 ? $this->specialAbilityIndex = 4 : $this->specialAbilityIndex--;
            }
            if ($event->column === 16 && $event->row === 14) {
                $this->specialAbilityIndex === 4 ? $this->specialAbilityIndex = 0 : $this->specialAbilityIndex++;
            }


            $areas = [
                'race_human' => [1,1,34,1],
                'race_lizzard' => [1,2,34,2],
                'race_gnome' => [1,3,34,3],
                'class_warrior' => [1,6,34,6],
                'class_wizard' => [1,7,34,7],
                'gender_men' => [1,10,34,10],
                'gender_women' => [1,11,34,11],
                'start' => [109,38,143,38],
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
                            GridWidget::default()
                                ->constraints(
                                    Constraint::min(1),
                                    Constraint::min(10),
                                )
                                ->widgets(
                                    ParagraphWidget::fromString('⮘ ' . $this->getAbilityName() . ' ⮚'),
                                    ParagraphWidget::fromString($this->getAbilityDescription()),
                                )
                        ),
                    BlockWidget::default()
                        ->borders(Borders::ALL)
                        ->borderType(BorderType::Rounded)
                        ->titles(Title::fromString('Параметры'))
                    ->widget($this->getParamGrid())
                ),
                BlockWidget::default()
                    ->borders(Borders::ALL)
                    ->borderType(BorderType::Rounded)
                    ->titles(Title::fromString('Портрет'))
                    ->widget(
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

    private function getParamGrid(): GridWidget
    {
        return GridWidget::default()
            ->constraints(
                Constraint::max(1),
                Constraint::max(1),
                Constraint::max(1),
                Constraint::max(1),
                Constraint::max(1),
                Constraint::max(1),
                Constraint::max(1),
                Constraint::max(1),
            )
            ->widgets(
            ParagraphWidget::fromString('Сила         ⮘ 10 ⮚'),
            ParagraphWidget::fromString('Ловкость     ⮘ 10 ⮚'),
            ParagraphWidget::fromString('Телосложение ⮘ 10 ⮚'),
            ParagraphWidget::fromString('Интеллект    ⮘ 10 ⮚'),
            ParagraphWidget::fromString('Мудрость     ⮘ 10 ⮚'),
            ParagraphWidget::fromString('Харизма      ⮘ 10 ⮚'),
            ParagraphWidget::fromString(''),
            ParagraphWidget::fromString('Не иcпользовано очков: 10'),
        );
    }
}
