<?php


namespace Saloodo\Scheduler\Contract;

interface LockInterface
{
    /**
     * @param JobInterface $job
     * @return bool
     */
    public function tryLock(JobInterface $job): bool;

    /**
     * @param JobInterface $job
     * @return bool
     */
    public function unlock(JobInterface $job);
}
