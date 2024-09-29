<?php

namespace Game\RoleSystem\Social;

// владение языком - разговорное и письменное
// минусы - за родной язык
enum LanguageLevel
{
    case NO;
    case BROKEN; // штраф -3 на умения с этим языком, стоймость: +-1 очко
    case ACCENT; // штраф -1 на умения с этим языком, стоймость: +-2 очка
    case NATIVE; // стоймость: +-3 очка
}