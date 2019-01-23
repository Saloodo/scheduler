<?php

namespace Saloodo\Scheduler\Jobs;

use Saloodo\Scheduler\Contract\JobInterface;

abstract class AbstractJob implements JobInterface
{
    /**
     * @var Schedule
     */
    private $schedule;

    protected $shouldRunOnOnlyOneServer = true;
    protected $canOverlap = false;

    public function __construct()
    {
        $this->schedule = new Schedule();
        $this->initialize($this->schedule);
    }

    /**
     * @param \Datetime|string $currentTime
     * @return bool
     */
    public function isDue($currentTime): bool
    {
        return $this->schedule->isDue($currentTime);
    }

    public function getSchedule(): Schedule
    {
        return $this->schedule;
    }

    public function getUniqueId(): string
    {
        return substr(sha1(get_class($this)), 0, 10);
    }

    public function getName(): string
    {
        return get_called_class();
    }

    abstract protected function initialize(Schedule $schedule);
}
