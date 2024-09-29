<?php

namespace Game\RoleSystem;

use Game\RoleSystem\Contract\CharacterInterface;
use Game\RoleSystem\Stats\Attributes;
use Game\RoleSystem\Stats\Gender;
use Game\RoleSystem\Stats\Race;
use Game\RoleSystem\Stats\Speciality;

abstract class Character implements CharacterInterface
{
    protected int $health;
    protected int $mana;
    protected int $level;
    protected int $experience;

    public function __construct(
        protected Race       $race,
        protected Attributes $attributes,
        protected Gender     $gender,
        protected string     $name,
        protected Speciality $speciality = Speciality::NEUTRAL,
    ) {
        if ($race === Race::DWARF) {
            $this->attributes->constitution += 2;
        }
        if ($race === Race::LIZARD) {
            $this->attributes->strength += 2;
            $this->attributes->charisma += 1;
        }
        if ($race === Race::HUMAN) {
            foreach ($this->attributes as &$char) {
                $char += 1;
            }
        }

        $healthBonus = $this->attributes->getModificator('constitution');
        $manaBonus = $this->attributes->getModificator('intelligence');
        $this->health = max($healthBonus, 0);
        $this->mana = max($manaBonus, 0);

        $this->health += match ($this->speciality) {
            Speciality::WARRIOR => 10,
            default => 8,
        };
        $this->mana += match ($this->speciality) {
            Speciality::WIZARD => 10,
            default => 8,
        };
        $this->level = 1;
        $this->experience = 0;
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

    public function levelUp(?string $selectedBonus = null): self
    {
        $this->level += 1;

        if ($this->level % 4 === 0) {
            if ($selectedBonus === null) {
                match ($this->speciality) {
                    Speciality::WARRIOR => $this->attributes->constitution += 1,
                    Speciality::WIZARD => $this->attributes->intelligence += 1,
                    Speciality::NEUTRAL => null,
                };
            } else {
                $this->attributes->{$selectedBonus} += 1;
            }
        }

        $healthBonus = $this->attributes->getModificator('constitution');
        $manaBonus = $this->attributes->getModificator('intelligence');
        $this->health += max($healthBonus, 0);
        $this->mana += max($manaBonus, 0);

        $this->health += match ($this->speciality) {
            Speciality::WARRIOR => rollDices(1, 10),
            default => rollDices(1, 8),
        };
        $this->mana += match ($this->speciality) {
            Speciality::WIZARD, => rollDices(1, 10),
            default => rollDices(1, 8),
        };

        return $this;
    }

    // TODO: пропуск / повторение попыток определяется здравым смыслом =)
    // TODO: величина успеха помогает в состязаниях. Само состязание может проходить по разной логике, разное время
    public function success(Skill|Attributes $stat, int $modifier = 0): array
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
    public function attitude(int $modifier = 0): Attitude
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