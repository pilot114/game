<?php

namespace Game\RoleSystem\Contract;

/**
 * Определяет, как создается и развивается персонаж на основе распределения очков опыта
 */
interface CharacterProgressInterface
{
    public function distribute(int $experiencePoint, CharacterInterface $character): CharacterInterface;
}
