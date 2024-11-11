<?php

namespace Game\RoleSystem;

use Game\RoleSystem\Stats\Attributes;
use Game\RoleSystem\Stats\Gender;
use Game\RoleSystem\Stats\Race;
use Game\RoleSystem\Stats\Speciality;

class NPC extends Character
{
    public function __construct(
        protected Race       $race,
        protected Attributes $attributes,
        protected Gender     $gender,
        protected string     $name,
        protected Speciality $speciality,
        protected int        $abilityId,
        protected int        $portraitId,
    ) {
        if ($speciality === Speciality::NEUTRAL) {
            throw new \LogicException('У NPC должна быть специализация');
        }
        parent::__construct($race, $attributes, $gender, $name, $speciality);
    }
}