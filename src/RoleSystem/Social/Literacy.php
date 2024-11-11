<?php

namespace Game\RoleSystem\Social;

// грамотность
enum Literacy
{
    case NO; // не умеете читать
    case HALF; // нужна проверка, чтобы понять смысл
    case NORMAL;
}