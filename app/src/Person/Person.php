<?php

namespace Game\Person;

abstract class Person
{
    protected int $health;
    protected int $mana;
    protected int $level;
    protected int $experience;

    public function __construct(
        protected Race $race,
        protected Chars $chars,
        protected Gender $gender,
        protected string $name,
        protected Speciality $speciality = Speciality::NEUTRAL,
    ) {
        if ($race === Race::DWARF) {
            $this->chars->constitution += 2;
        }
        if ($race === Race::LIZARD) {
            $this->chars->strength += 2;
            $this->chars->charisma += 1;
        }
        if ($race === Race::HUMAN) {
            foreach ($this->chars as &$char) {
                $char += 1;
            }
        }

        $healthBonus = $this->chars->getModificator('constitution');
        $manaBonus = $this->chars->getModificator('intelligence');
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
        $info[] = json_encode($this->chars);

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
                    Speciality::WARRIOR => $this->chars->constitution += 1,
                    Speciality::WIZARD => $this->chars->intelligence += 1,
                    Speciality::NEUTRAL => null,
                };
            } else {
                $this->chars->{$selectedBonus} += 1;
            }
        }

        $healthBonus = $this->chars->getModificator('constitution');
        $manaBonus = $this->chars->getModificator('intelligence');
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
}