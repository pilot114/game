<?php

namespace Game\UI;

class PageEvent
{
    public function __construct(
        public PageEventType $eventType,
        public mixed $data = null,
    ) {
    }
}