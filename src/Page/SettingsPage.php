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
use PhpTui\Tui\Model\Direction;
use PhpTui\Tui\Model\Display\Display;
use PhpTui\Tui\Model\HorizontalAlignment;
use PhpTui\Tui\Model\Layout\Constraint;
use PhpTui\Tui\Model\Text\Text;
use PhpTui\Tui\Model\Widget\Borders;
use PhpTui\Tui\Model\Widget\BorderType;

class SettingsPage implements PageInterface
{
    public function handle(Event $event): ?PageInterface
    {
        return null;
    }

    public function render(Display $display): void
    {
        $display->draw(
            ParagraphWidget::fromString('settings'),
        );
    }
}
