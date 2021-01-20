<?php

namespace Saloodo\Scheduler\Event;

use Saloodo\Scheduler\Contract\JobInterface;
use Throwable;

class JobFailedEvent extends Event
{
    const NAME = 'job.failed';

    private $throwable;
    private $job;

    public function __construct(JobInterface $job, Throwable $throwable)
    {
        $this->throwable = $throwable;
        $this->job = $job;
    }

    public function getJob(): JobInterface
    {
        return $this->job;
    }

    public function getThrowable(): Throwable
    {
        return $this->throwable;
    }
}
