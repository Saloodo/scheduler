<?php

namespace Saloodo\Scheduler\Event;

use Saloodo\Scheduler\Contract\JobInterface;
use Symfony\Contracts\EventDispatcher\Event;

class JobSkippedEvent extends Event
{
    const NAME = 'job.skipped';

    const SERVER_SHOULD_NOT_RUN = 'SERVER_SHOULD_NOT_RUN';
    const WOULD_OVERLAP = 'WOULD_OVERLAP';

    private $job;
    private $reason;

    public function __construct(JobInterface $job, string $reason)
    {
        $this->job = $job;
        $this->reason = $reason;
    }

    public function getJob(): JobInterface
    {
        return $this->job;
    }

    public function getReason(): string
    {
        return $this->reason;
    }
}
