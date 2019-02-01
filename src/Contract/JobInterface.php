<?php


namespace Saloodo\Scheduler\Contract;

use Saloodo\Scheduler\Jobs\Schedule;

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
}
