<?php

namespace Game\RoleSystem;

use Game\RoleSystem\Contract\CharacterInterface;
use Game\RoleSystem\Social\Attitude;
use Game\RoleSystem\Social\Wealth;
use Game\RoleSystem\Stats\Attributes;
use Game\RoleSystem\Stats\Gender;
use Game\RoleSystem\Stats\Race;
use Game\RoleSystem\Stats\Skill;
use Game\RoleSystem\Stats\Speciality;

abstract class Character implements CharacterInterface
{
    protected int $health;
    protected int $mana;
    protected int $experiencePoints;
    protected TechLevel $techLevel; // TODO: как влияет? стоймость: +-5 очков

    protected Wealth $wealth; // богатство
    protected int $reputation; // репутация. от-4до+4. За каждый левел 5 очков
    protected int $influence; // влиятельность, статус. от -2 (раб) до 8 (король). За каждый левел 5 очков

    public function __construct(
        protected Race       $race,
        protected Attributes $attributes,
        protected Gender     $gender,
        protected string     $name,
        protected Speciality $speciality = Speciality::NEUTRAL,
    ) {
    }

    public function __toString(): string
    {
        $info = [];
        $info[] = "'$this->name'";
        $info[] = match ($this->gender) {
            Gender::MALE => '(M)',
            Gender::FEMALE => '(Ж)',
        };
        $info[] = match ($this->race) {
            Race::HUMAN => 'Человек',
            Race::LIZARD => 'Ящер',
            Race::DWARF => 'Гном',
        };
        $info[] = match ($this->speciality) {
            Speciality::WARRIOR => 'Воин',
            Speciality::WIZARD => 'Маг',
            Speciality::NEUTRAL => '',
        };
        $info[] = "Уровень: $this->level";
        $info[] = json_encode($this->attributes);

        $info[] = "HP: $this->health";
        $info[] = "MP: $this->mana";
        return implode(' ', $info);
    }

    // TODO: пропуск / повторение попыток определяется здравым смыслом =)
    // TODO: величина успеха помогает в состязаниях. Само состязание может проходить по разной логике, разное время
    public function stats(Skill|Attributes $stat, int $modifier = 0): array
    {
        $effectiveValue = $this->getStatValue($stat) + $modifier;

        // TODO: для броска защиты это условие не применяется!
        if ($effectiveValue < 3) {
            return [false, false];
        }

        $check = array_sum([
            mt_rand(1, 6),
            mt_rand(1, 6),
            mt_rand(1, 6),
        ]);

        $isSuccess = $check <= $effectiveValue;

        if ($isSuccess) {
            $isCritical = match (true) {
                $effectiveValue >= 16 => $check === 6,
                $effectiveValue >= 15 => $check === 5,
                default => $check < 5
            };
        } else {
            $isCritical = match (true) {
                $check === 18 => true,
                $check === 17 => $effectiveValue <= 15,
                default => ($check - 10) >= $effectiveValue
            };
        }

        // признак успеха, признак крита, величина
        return [ $isSuccess, $isCritical, $effectiveValue - $check];
    }

    // отношение к персонажу
    // TODO: внешность, расы, поведение
    // TODO: Голос (за 10 очков, +2 за разговорные умения, +2 на social если вас слышат)
    // TODO: Харизма (5 очков за каждый уровень) +1 social за каждый уровень, +1 броски влияния, +1 к умениям Лидерство/Публичное выступление
    public function social(int $modifier = 0): Attitude
    {
        $check = array_sum([
            mt_rand(1, 6),
            mt_rand(1, 6),
            mt_rand(1, 6),
        ]);
        $result = $check + $modifier;

        return Attitude::fromValue($result);
    }

    public function damage(int $countDices, int $modifier = 0): int
    {
        $result = 0;
        while ($countDices--) {
            $result += mt_rand(1, 6);
        }
        return $result + $modifier;
    }

    protected function getStatValue(Skill|Attributes $stat): int
    {
        // TODO
        return 42;
    }
}