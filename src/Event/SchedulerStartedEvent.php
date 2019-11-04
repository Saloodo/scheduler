<?php

namespace Saloodo\Scheduler\Event;

use Symfony\Contracts\EventDispatcher\Event;

class SchedulerStartedEvent extends Event
{
    const NAME = 'job.scheduler.started';
}
