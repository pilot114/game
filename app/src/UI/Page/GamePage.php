<?php

namespace Game\UI\Page;

use Game\UI\AbstractPage;
use PhpTui\Tui\Extension\Core\Widget\ParagraphWidget;

class GamePage extends  AbstractPage
{
    public function draw(): void
    {
        $this->display->draw(
            ParagraphWidget::fromString('game'),
        );
    }
}
