<?php

use Game\{Controller, UI, World, Player};

require_once './vendor/autoload.php';

$ui = new UI();
$player = new Player($ui);
$world = new World($ui, $player);
$game = new Controller($ui, $player, $world);
$game->start();