<?php

namespace Game\UI;

enum PageEventType
{
    case Stop;
    case ChangePage;
    case NeedDraw;
}