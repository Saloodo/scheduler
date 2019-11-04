<?php

namespace Saloodo\Scheduler\Event;

use Symfony\Contracts\EventDispatcher\Event;

class SchedulerCompletedEvent extends Event
{
    const NAME = 'job.scheduler.completed';
}
