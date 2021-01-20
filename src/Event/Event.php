<?php

namespace Saloodo\Scheduler\Event;

use Symfony\Component\EventDispatcher\Event as BaseEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Contracts\EventDispatcher\Event as ContractsBaseEvent;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * Compatibility with symfony 3.4 and ^4
 */

if (is_subclass_of(EventDispatcher::class, EventDispatcherInterface::class)) {
    class Event extends ContractsBaseEvent
    {
    }
} else {
    class Event extends BaseEvent
    {
    }
}