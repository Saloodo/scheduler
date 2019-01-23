<?php


namespace Saloodo\Scheduler\Contract;

use Saloodo\Scheduler\Jobs\Schedule;

interface JobInterface
{
    public function isDue($currentTime): bool;

    public function run();

    public function getUniqueId(): string;

    public function getName(): string;

    public function getSchedule(): Schedule;
}
