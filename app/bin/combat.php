<?php

interface CharacterInterface
{
    public function getName(): string;
    public function getHealthPoints(): int;
    public function getWeapons(): array;
    public function takeDamage(int $amount): void;
    public function performSkillCheck(string $skillName, int $difficulty): bool;
}

interface WeaponInterface
{
    public function getName(): string;
    public function useWeapon(): int;
}

interface SkillInterface
{
    public function getName(): string;
    public function getLevel(): int;
    public function useSkill(int $difficulty): bool;
}

class Skill implements SkillInterface {
    public function __construct(
        protected string $name,
        protected int $level
    ) {
    }

    public function getName(): string { return $this->name; }

    public function getLevel(): int { return $this->level; }

    public function useSkill(int $difficulty): bool
    {
        $roll = random_int(1, 6) + random_int(1, 6) + random_int(1, 6);
        return $roll <= $this->level - $difficulty;
    }
}

class MainAttribute {

    public function __construct(
        protected string $name,
        protected int $value
    ) {
    }

    public function getValue(): int {
        return $this->value;
    }
}

class Weapon implements WeaponInterface
{
    public function __construct(
        protected string $name,
        protected string $damage
    ) {
    }

    public function getName(): string { return $this->name; }

    public function useWeapon(): int
    {
        preg_match('/(\d+)d(\d+)([+-]\d+)?/', $this->damage, $matches);
        [$diceCount, $diceSides, $modifier] = [(int)$matches[1], (int)$matches[2], $matches[3] ?? 0];
        $damage = array_sum(array_map(fn() => random_int(1, $diceSides), range(1, $diceCount)));
        return $damage + (int)$modifier;
    }
}

class Character implements CharacterInterface
{

    public function __construct(
        protected string $name,
        protected array $attributes,
        protected array $skills,
        protected int $healthPoints,
        protected array $weapons = []
    ) {
    }

    public function getName(): string { return $this->name; }
    public function getHealthPoints(): int { return $this->healthPoints; }
    public function getWeapons(): array { return $this->weapons; }

    public function takeDamage(int $amount): void
    {
        $this->healthPoints = max(0, $this->healthPoints - $amount);
    }

    public function performSkillCheck(string $skillName, int $difficulty): bool
    {
        foreach ($this->skills as $skill) {
            if ($skill->getName() === $skillName) return $skill->useSkill($difficulty);
        }
        return false;
    }
}

interface CombatInterface {
    public function attack(CharacterInterface $attacker, CharacterInterface $defender, WeaponInterface $weapon): string; // Выполнение атаки
    public function defend(CharacterInterface $defender, string $defenseType): bool; // Защита (уклонение, парирование, блок)
    public function resolveDamage(CharacterInterface $defender, int $damage): void; // Применение урона к защите
}

class Combat implements CombatInterface
{
    public function startCombat(array $characters): void
    {
        $turn = 0;
        while (!$this->isCombatOver($characters)) {
            $attacker = $characters[$turn % count($characters)];
            if ($attacker->getHealthPoints() <= 0) { $turn++; continue; }

            $defender = $this->chooseDefender($attacker, $characters);
            if ($defender) echo $this->performTurn($attacker, $defender);

            $turn++;
        }
        echo "Бой завершен!\n";
        foreach ($characters as $character) {
            if ($character->getHealthPoints() > 0)
                echo "{$character->getName()} выжил с {$character->getHealthPoints()} очками здоровья!\n";
        }
    }

    public function chooseDefender(CharacterInterface $attacker, array $characters): ?CharacterInterface
    {
        $validTargets = array_filter($characters, fn($char) => $char !== $attacker && $char->getHealthPoints() > 0);
        return count($validTargets) ? $validTargets[array_rand($validTargets)] : null;
    }

    public function performTurn(CharacterInterface $attacker, CharacterInterface $defender): string
    {
        $weapon = $attacker->getWeapons()[0];
        return $this->defend($defender, 'Dodge') ? "{$defender->getName()} уклоняется!\n" : $this->attack($attacker, $defender, $weapon);
    }

    public function attack(CharacterInterface $attacker, CharacterInterface $defender, WeaponInterface $weapon): string
    {
        if ($attacker->performSkillCheck("Melee", 8)) {
            $damage = $weapon->useWeapon();
            $this->resolveDamage($defender, $damage);
            return "{$attacker->getName()} атакует с {$weapon->getName()} и наносит {$damage} урона.\n";
        }
        return "{$attacker->getName()} промахивается.\n";
    }

    public function defend(CharacterInterface $defender, string $defenseType): bool
    {
        return $defender->performSkillCheck($defenseType, 10);
    }

    public function resolveDamage(CharacterInterface $defender, int $damage): void
    {
        $defender->takeDamage($damage);
    }

    public function isCombatOver(array $characters): bool
    {
        return count(array_filter($characters, fn($char) => $char->getHealthPoints() > 0)) <= 1;
    }
}

class CharacterFactory {

    public function __construct(
        protected array $availableWeapons,
        protected array $availableSkills
    ) {
    }

    public function createRandomCharacter(string $name): CharacterInterface
    {
        $attributes = $this->generateRandomAttributes();
        $skills = $this->generateRandomSkills();
        $weapons = $this->generateRandomWeapons();

        // Устанавливаем случайные очки здоровья, например, от 15 до 25
        $hp = random_int(15, 25);

        return new Character($name, $attributes, $skills, $hp, $weapons);
    }

    protected function generateRandomAttributes(): array
    {
        return [
            'ST' => new MainAttribute('ST', random_int(8, 14)), // Сила
            'DX' => new MainAttribute('DX', random_int(8, 14)), // Ловкость
            'IQ' => new MainAttribute('IQ', random_int(8, 14)), // Интеллект
            'HT' => new MainAttribute('HT', random_int(8, 14)), // Здоровье
        ];
    }

    protected function generateRandomSkills(): array
    {
        $skills = [];
        // Выбираем случайные навыки из доступного списка
        foreach ($this->availableSkills as $skillName => $levelRange) {
            $skills[] = new Skill($skillName, random_int($levelRange['min'], $levelRange['max']));
        }
        return $skills;
    }

    protected function generateRandomWeapons(): array
    {
        // Выбираем случайное оружие
        $weaponKeys = array_rand($this->availableWeapons, random_int(1, 2));
        if (!is_array($weaponKeys)) {
            $weaponKeys = [$weaponKeys];
        }
        return array_map(fn($key) => $this->availableWeapons[$key], $weaponKeys);
    }
}

// Пример использования

$availableWeapons = [
    new Weapon('Sword', '1d6+2'),
    new Weapon('Dagger', '1d4+1'),
    new Weapon('Axe', '1d6+3'),
    new Weapon('Mace', '1d8'),
];

$availableSkills = [
    'Melee' => ['min' => 10, 'max' => 14],
    'Dodge' => ['min' => 8, 'max' => 12],
    'Parry' => ['min' => 8, 'max' => 12],
];

$factory = new CharacterFactory($availableWeapons, $availableSkills);

$warrior1 = $factory->createRandomCharacter('Warrior 1');
$warrior2 = $factory->createRandomCharacter('Warrior 2');
$warrior3 = $factory->createRandomCharacter('Warrior 3');

$combat = new Combat();
$combat->startCombat([$warrior1, $warrior2, $warrior3]);
