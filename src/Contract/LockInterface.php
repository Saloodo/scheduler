<?php


namespace Saloodo\Scheduler\Contract;

interface LockInterface
{
    public function tryLock(JobInterface $job): bool;

    public function unlock(JobInterface $job);
}
