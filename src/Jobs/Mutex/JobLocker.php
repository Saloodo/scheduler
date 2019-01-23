<?php

namespace Saloodo\Scheduler\Jobs\Mutex;

use DateTimeImmutable;
use Saloodo\Scheduler\Contract\JobInterface;
use Saloodo\Scheduler\Contract\LockInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;

/**
 *
 */
class JobLocker implements LockInterface
{
    protected $cache;

    public function __construct(AdapterInterface $cache)
    {
        $this->cache = $cache;
    }

    public function tryLock(JobInterface $job, $time = null): bool
    {
        if (!$time) {
            $time = new DateTimeImmutable();
        }

        $info = $this->cache->getItem($job->getUniqueId());

        if ($info->isHit()) {
            //cache is hit, cannot lock!
            return false;
        }

        //content does not matter
        $info->set(null);

        $info->expiresAt($time->modify('+30 minutes'));

        // and saves it
        return $this->cache->save($info);
    }

    public function unlock(JobInterface $job): bool
    {
        return $this->cache->deleteItem($job->getUniqueId());
    }
}
