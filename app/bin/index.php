<?php
declare(strict_types=1);

include __DIR__ . '/../vendor/autoload.php';

use Game\{Player, UI\Controller, UI\UI, World};

$ui = new UI();
$player = new Player($ui);
$world = new World($ui, $player);
$game = new Controller($ui, $player, $world);
$game->handleTerminal();
