<?php
declare(strict_types=1);

require_once './vendor/autoload.php';

use Game\{Controller, UI, World, Player};

$ui = new UI();
$player = new Player($ui);
$world = new World($ui, $player);
$game = new Controller($ui, $player, $world);
$game->handleTerminal();
