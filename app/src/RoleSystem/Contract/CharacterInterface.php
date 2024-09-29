<?php

namespace Game\RoleSystem\Contract;

use Game\RoleSystem\Social\Attitude;
use Game\RoleSystem\Stats\Attributes;
use Game\RoleSystem\Stats\Skill;

interface CharacterInterface
{
    /**
     * Набор проверок персонажа (все d6)
     */

    /**
     * Проверка умений или аттрибутов (базовое умение + модификатор = эффективное умение)
     * Если модификаторов несколько - они суммируются
     */
    public function stats(Skill|Attributes $stat, int $modifier = 0): array;
    public function social(int $modifier = 0): Attitude;
    public function damage(int $countDices, int $modifier = 0): int;
}