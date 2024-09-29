<?php

namespace Game\RoleSystem;

use Game\RoleSystem\Stats\Attributes;
use Game\RoleSystem\Stats\Gender;
use Game\RoleSystem\Stats\Race;
use Game\RoleSystem\Stats\Speciality;

class Player extends Character
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
            throw new \LogicException('У персонажа должна быть специализация');
        }
        parent::__construct($race, $attributes, $gender, $name, $speciality);
    }
}