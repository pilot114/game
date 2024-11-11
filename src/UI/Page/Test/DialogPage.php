<?php

namespace Game\UI\Page\Test;

use Game\UI\AbstractPage;
use Game\UI\PageEvent;
use Game\UI\PageInterface;
use PhpTui\Term\Event;
use PhpTui\Tui\DisplayBuilder;
use PhpTui\Tui\Extension\Core\Widget\BlockWidget;
use PhpTui\Tui\Extension\Core\Widget\GridWidget;
use PhpTui\Tui\Extension\Core\Widget\ParagraphWidget;
use PhpTui\Tui\Extension\ImageMagick\Widget\ImageWidget;
use PhpTui\Tui\Layout\Constraint;
use PhpTui\Tui\Style\Style;
use PhpTui\Tui\Text\Title;
use PhpTui\Tui\Widget\Borders;
use PhpTui\Tui\Widget\Direction;

class DialogPage extends AbstractPage implements PageInterface
{
    public function handle(Event $event): ?PageEvent
    {
        return null;
    }

    public function draw(): void
    {
        $this->display->draw(
            GridWidget::default()
            ->direction(Direction::Horizontal)
            ->constraints(
                Constraint::percentage(50),
                Constraint::percentage(50),
            )
            ->widgets(
                ImageWidget::fromPath(__DIR__ . "/../../../resources/images/hero/gnome_men_wizard_resized.webp"),
                ImageWidget::fromPath(__DIR__ . "/../../../resources/images/hero/gnome_men_wizard_resized.webp"),
            )
        );

        $display = DisplayBuilder::default()->fixed(20, 28, 110, 10)->build();
        $display->draw(
            BlockWidget::default()
                ->borders(Borders::ALL)
                ->titles(Title::fromString('Диалог с NPC'))
                ->style(Style::default()->yellow())
                ->widget(
                    ParagraphWidget::fromString("\nТекст диалога\n бла бла бла")->style(Style::default()->yellow())
                )
        );
    }
}
