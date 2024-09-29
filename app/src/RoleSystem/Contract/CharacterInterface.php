<?php

namespace Game\RoleSystem\Contract;

use Game\RoleSystem\Attitude;
use Game\RoleSystem\Skill;
use Game\RoleSystem\Stats\Attributes;

interface CharacterInterface
{
    /**
     * Набор проверок персонажа (все d6)
     */

    /**
     * Проверка умений или аттрибутов (базовое умение + модификатор = эффективное умение)
     * Если модификаторов несколько - они суммируются
     */
    public function success(Skill|Attributes $stat, int $modifier = 0): array;
    public function attitude(int $modifier = 0): Attitude;
    public function damage(int $countDices, int $modifier = 0): int;
}