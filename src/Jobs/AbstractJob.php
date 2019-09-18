<?php

namespace Saloodo\Scheduler\Jobs;

use Saloodo\Scheduler\Contract\JobInterface;
use Symfony\Component\Lock\Lock;

abstract class AbstractJob implements JobInterface
{
    private $schedule;

    /** @var int */
    private $startTime;

    /** @var int */
    private $endTime;

    /**
     * @var Lock
     */
    private $lock;

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

    /**
     * @return Schedule
     */
    public function getSchedule(): Schedule
    {
        return $this->schedule;
    }

    /**
     * @return string
     */
    public function getUniqueId(): string
    {
        return substr(sha1(get_class($this)), 0, 10);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return get_called_class();
    }

    /**
     * @return int
     */
    public function getStartTime(): int
    {
        return $this->startTime;
    }

    /**
     * @param int $startTime
     * @return JobInterface
     */
    public function setStartTime(int $startTime): JobInterface
    {
        $this->startTime = $startTime;
        return $this;
    }

    /**
     * @return int
     */
    public function getEndTime(): int
    {
        return $this->endTime;
    }

    /**
     * @param int $endTime
     * @return JobInterface
     */
    public function setEndTime(int $endTime): JobInterface
    {
        $this->endTime = $endTime;
        return $this;
    }

    public function setLock(Lock $lock)
    {
       $this->lock = $lock;
    }

    public function getLock(): Lock
    {
        return  $this->lock;
    }

    /**
     * @param Schedule $schedule
     * @return mixed
     */
    abstract protected function initialize(Schedule $schedule);
}
