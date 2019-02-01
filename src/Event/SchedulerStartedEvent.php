<?php

namespace Saloodo\Scheduler\Event;

use Symfony\Component\EventDispatcher\Event;

class SchedulerStartedEvent extends Event
{
    const NAME = 'job.scheduler.started';
}
