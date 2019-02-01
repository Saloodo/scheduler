<?php

namespace Saloodo\Scheduler\Jobs;

use Saloodo\Scheduler\Contract\JobInterface;

abstract class AbstractJob implements JobInterface
{
    private $schedule;

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
     * @param Schedule $schedule
     * @return mixed
     */
    abstract protected function initialize(Schedule $schedule);
}
