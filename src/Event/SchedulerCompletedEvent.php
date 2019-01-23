<?php

namespace Saloodo\Scheduler\Event;

use Symfony\Component\EventDispatcher\Event;

class SchedulerCompletedEvent extends Event
{
    const NAME = 'job.scheduler.completed';

    public function __construct()
    {

    }
}
