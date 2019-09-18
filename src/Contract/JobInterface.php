<?php


namespace Saloodo\Scheduler\Contract;

use Saloodo\Scheduler\Jobs\Schedule;
use Symfony\Component\Lock\Lock;

interface JobInterface
{
    /**
     * @param $currentTime
     * @return bool
     */
    public function isDue($currentTime): bool;

    /**
     * @return mixed
     */
    public function run();

    /**
     * @return string
     */
    public function getUniqueId(): string;

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return Schedule
     */
    public function getSchedule(): Schedule;

    /**
     * @return int
     */
    public function getStartTime(): int;

    /**
     * @param int $startTime
     * @return self
     */
    public function setStartTime(int $startTime): self;


    /**
     * @return int
     */
    public function getEndTime(): int;


    /**
     * @param int $endTime
     * @return self
     */
    public function setEndTime(int $endTime): self;

    /**
     * @param Lock $lock
     * @return mixed
     */
    public  function setLock (Lock $lock);

    /**
     * @return mixed
     */
    public function getLock();
}
