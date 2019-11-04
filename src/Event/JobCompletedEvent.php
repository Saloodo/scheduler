<?php

namespace Saloodo\Scheduler\Event;

use Saloodo\Scheduler\Contract\JobInterface;
use Symfony\Contracts\EventDispatcher\Event;


class JobCompletedEvent extends Event
{
    const NAME = 'job.completed';

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
