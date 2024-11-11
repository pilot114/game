<?php

use CuyZ\Valinor\Mapper\MappingError;
use CuyZ\Valinor\Mapper\Tree\Message\Messages;
use CuyZ\Valinor\MapperBuilder;
use Game\Quest\Location;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\Yaml\Yaml;

include __DIR__ . '/../vendor/autoload.php';

// ---------- загрузка квеста ---------- //

$questDataFile = __DIR__ . '/../resources/data/edge.yaml';
$questData = Yaml::parseFile($questDataFile);

try {
    $location = (new MapperBuilder())
        ->mapper()
        ->map(Location::class, $questData);
} catch (MappingError $error) {
    $messages = Messages::flattenFromNode($error->node());
    $errorMessages = $messages->errors();
    foreach ($errorMessages as $message) {
        echo $message->node()->path() . ":\n\t" . $message . "\n";
    }
    exit;
}

// ---------- эмуляция квеста ---------- //

/** @var Location $location */

$location->setDispatcher(new EventDispatcher());

// активные квесты
$quests = $location->getActiveQuests();

// Нужен объект журнала, которые toString + интерфейс эмуляции событий?

// получаем действия, проверки которых выполняются. Если они ещё не в журнале - добавляем
// $quest->getActualActions();

// выполняем события
// $dispatcher->emit();

// список поменялся - журнал обновился
// $quest->getActualActions();
