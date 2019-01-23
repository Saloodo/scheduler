<?php

namespace Saloodo\Scheduler\Event;

use Saloodo\Scheduler\Contract\JobInterface;
use Symfony\Component\EventDispatcher\Event;

class JobStartedEvent extends Event
{
    const NAME = 'job.started';

    private $job;

    public function __construct(JobInterface $job)
    {
        $this->job = $job;
    }

    public function getJob(): JobInterface
    {
        return $this->job;
    }
}
