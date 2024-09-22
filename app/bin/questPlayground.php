<?php

use CuyZ\Valinor\Mapper\MappingError;
use CuyZ\Valinor\Mapper\Tree\Message\Messages;
use CuyZ\Valinor\MapperBuilder;
use Game\Quest\Location;
use Symfony\Component\Yaml\Yaml;

include __DIR__ . '/../vendor/autoload.php';


// ---------- загрузка квеста ---------- //

$questDataFile = __DIR__ . '/../resources/data/edge.yaml';
try {
    $quest = (new MapperBuilder())
        ->mapper()
        ->map(Location::class, Yaml::parseFile($questDataFile));
} catch (MappingError $error) {
    $messages = Messages::flattenFromNode($error->node());
    $errorMessages = $messages->errors();
    foreach ($errorMessages as $message) {
        echo $message->node()->path() . ":\n\t" . $message . "\n";
    }
    exit;
}

// ---------- эмуляция квеста ---------- //

//dispatcher: new EventDispatcher(),
//        language: new ExpressionLanguage(),

dump($quest);
