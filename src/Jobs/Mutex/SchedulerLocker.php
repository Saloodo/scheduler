<?php

namespace Saloodo\Scheduler\Jobs\Mutex;

use DateTimeImmutable;
use Saloodo\Scheduler\Contract\JobInterface;
use Saloodo\Scheduler\Contract\LockInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;

/**
 *
 */
class SchedulerLocker implements LockInterface
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
        $key = $job->getUniqueId() . '_' . $time->format('Hi');

        $info = $this->cache->getItem($key);

        if ($info->isHit()) {
            //cache is hit, cannot lock!
            return false;
        }

        //content does not matter
        $info->set('');

        $info->expiresAt($time->modify('+5 minutes'));

        // and saves it
        return $this->cache->save($info);
    }

    public function unlock(JobInterface $job)
    {
        // TODO: Implement unlock() method.
    }
}
