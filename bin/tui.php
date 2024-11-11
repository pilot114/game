<?php

declare(strict_types=1);

use Game\Audio;
use Game\UI\Page\MainPage;
use Game\UI\PageController;

include __DIR__ . '/../vendor/autoload.php';

// TODO: прикрутить основной движок, сделать все TODO

Audio::startMusic('luma_dream_machine.wav');

$demo = new PageController(startPage: MainPage::class);
$demo->run();

Audio::stopMusic('luma_dream_machine.wav');
