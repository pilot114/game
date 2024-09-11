<?php

use Game\Person\Chars;
use Game\Person\Gender;
use Game\Person\Player;
use Game\Person\Race;
use Game\Person\Speciality;

include 'vendor/autoload.php';

/**
 * Скрипт для генерации игровых и неигровых персонажей (player / NPC)
 */

function rollDices(int $count, int $dice, ?int $top = null): int
{
    $results = [];
    while ($count) {
        $results[] = mt_rand(1, $dice);
        $count--;
    }
    if ($top !== null) {
        rsort($results);
        $results = array_slice($results, 0, $top);
    }
    return array_sum($results);
}

function randomItem(array $array): mixed
{
    $first = array_key_first($array);
    $last = array_key_last($array);
    $randKey = mt_rand($first, $last);
    return $array[$randKey];
}

// + https://donjon.bin.sh/fantasy/name/
function randomName(Race $race, Gender $gender): string
{
    $first = match(true) {
        $race === Race::HUMAN && $gender === Gender::MALE => randomItem([
            'Пол', 'Фейд-Раутха', 'Шаддам', 'Дункан', 'Стилгар', 'Паршек', 'Калеб', 'Дариус',
        ]),
        $race === Race::HUMAN && $gender === Gender::FEMALE => randomItem([
            'Чани', 'Льет', 'Хара', 'Алия', 'Глоссу', 'Корба', 'Джамис', 'Хелена',
        ]),
        $race === Race::LIZARD && $gender === Gender::MALE => randomItem([
            'Драг', 'Горотан', 'Верминакс', 'Зекал', 'Мордрак', 'Нивен',
        ]),
        $race === Race::LIZARD && $gender === Gender::FEMALE => randomItem([
            'Зира', 'Летисса', 'Дриира', 'Нираксис', 'Ворана'
        ]),
        $race === Race::DWARF && $gender === Gender::MALE => randomItem([
            'Барадин', 'Дурам', 'Ульвар', 'Воргрим', 'Кургар', 'Дарлин'
        ]),
        $race === Race::DWARF && $gender === Gender::FEMALE => randomItem([
            'Гримора', 'Тальмира', 'Ургильда', 'Бриланда', 'Арлина', 'Формира'
        ]),
    };
    $last = match (true) {
        $race === Race::HUMAN => randomItem([
            'Атрейдес', 'Харконнен', 'Коррино', 'Халлек', 'Айхадо', 'Кайнс', 'Туэк', 'Верниус', 'Ришез', 'Фенринг',
        ]),
        $race === Race::LIZARD && $gender === Gender::MALE => randomItem([
            'Кровавый Шип', 'Тенекоготь', 'Камнезуб', 'Тёмное Пламя', 'Вечная Тень',
        ]),
        $race === Race::LIZARD && $gender === Gender::FEMALE => randomItem([
            'Лунная Чешуя', 'Лесная Гроза', 'Пепельная Ветвь', 'Буря Огня', 'Ветер Сумрака'
        ]),
        $race === Race::DWARF => randomItem([
            'Нирод', 'Браге', 'Роткирх', 'Кронгельм', 'Гротенхельм', 'Веймарн'
        ]),
    };
    return "$first $last";
}

function createRandomPlayerWithLevel(int $level): Player
{
    $race = randomItem(Race::cases());
    $gender = randomItem(Gender::cases());
    $speciality = match (randomItem([1, 2])) {
        1 => Speciality::WARRIOR,
        2 => Speciality::WIZARD,
    };
    $chars = [
        'strength' => rollDices(4, 6, 3),
        'dexterity' => rollDices(4, 6, 3),
        'constitution' => rollDices(4, 6, 3),
        'charisma'=> rollDices(4, 6, 3),
        'intelligence' => rollDices(4, 6, 3),
        'wisdom' => rollDices(4, 6, 3),
    ];

    // гарантируем минимум 2 высоких параметра, минимум 1 - своей специализации
    if ($speciality === Speciality::WARRIOR) {
        $physical = randomItem(['strength', 'dexterity', 'constitution']);
        $chars[$physical] = 14 + rollDices(1, 4);

        $tmp = array_keys($chars);
        unset($tmp[$physical]);
        $chars[randomItem($tmp)] = 14 + rollDices(1, 4);
    }
    if ($speciality === Speciality::WIZARD) {
        $magical = randomItem(['charisma', 'intelligence', 'wisdom']);
        $chars[$magical] = 14 + rollDices(1, 4);

        $tmp = array_keys($chars);
        unset($tmp[$magical]);
        $chars[randomItem($tmp)] = 14 + rollDices(1, 4);
    }

    $chars = new Chars(...$chars);
    $name = randomName($race, $gender);
    $player = new Player(
        $race, $chars, $gender, $name, $speciality, 1, 2
    );
    while ($level > 1) {
        $player->levelUp();
        $level--;
    }
    return $player;
}

function append(array &$acc, object $obj, array $properties): void
{
    $r = new ReflectionObject($obj);
    foreach ($properties as $property) {
        $next = $r->getProperty($property)->getValue($obj);
        $acc[$property] = key_exists($property, $acc)
            ? ($acc[$property] + $next) / 2
            : $next;
    }
}

$eol = "\n";

$middle = [];
foreach (range(1, 10_000) as $i) {
    $player = createRandomPlayerWithLevel(1);
    append($middle, $player, [
        'health',
        'mana',
        'level',
        'experience',
    ]);
    //    echo $player . $eol;
}
dump($middle);

echo $eol;
