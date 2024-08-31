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

class DialogPage implements PageInterface
{
    public function handle(Event $event): ?PageInterface
    {
        return null;
    }

    public function render(Display $display): void
    {
        $display->draw(
            GridWidget::default()
            ->direction(Direction::Horizontal)
            ->constraints(
                Constraint::percentage(50),
                Constraint::percentage(50),
            )
            ->widgets(
                ImageWidget::fromPath(__DIR__ . "/../../resources/images/hero/gnome_men_wizard.webp"),
                ImageWidget::fromPath(__DIR__ . "/../../resources/images/hero/human_women_warrior.webp"),
            )
        );

        $display = DisplayBuilder::default()->fixed(20, 28, 110, 10)->build();
        $display->draw(
            BlockWidget::default()->borders(Borders::ALL)->titles(Title::fromString('Диалог с NPC'))
            ->style(Style::default()->yellow())
            ->widget(
                ParagraphWidget::fromString("\nТекст диалога\n бла бла бла")->style(Style::default()->yellow())
            )
        );
    }
}
