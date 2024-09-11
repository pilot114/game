<?php

namespace Game\Person;

class Player extends Person
{
    public function __construct(
        protected Race $race,
        protected Chars $chars,
        protected Gender $gender,
        protected string $name,
        protected Speciality $speciality,
        protected int $abilityId,
        protected int $portraitId,
    ) {
        if ($speciality === Speciality::NEUTRAL) {
            throw new \LogicException('У персонажа должна быть специализация');
        }
        parent::__construct($race, $chars, $gender, $name, $speciality);
    }
}