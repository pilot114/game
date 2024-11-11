<?php

namespace Game\RoleSystem\Contract;

/**
 * Определяет выборку объектов по доступности персонажу
 */
interface ObjectSelectorInterface
{
    public function sees(CharacterInterface $character): array;
    public function hears(CharacterInterface $character): array;
}