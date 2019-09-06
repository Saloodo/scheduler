<?php

namespace Saloodo\Scheduler\Jobs\Mutex;

use DateTimeImmutable;
use DateTimeInterface;
use Saloodo\Scheduler\Contract\JobInterface;
use Saloodo\Scheduler\Contract\LockInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;

class SchedulerLocker implements LockInterface
{
    protected $cache;



    public function __construct(AdapterInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @inheritdoc
     */
    public function tryLock(JobInterface $job, DateTimeInterface $time = null): bool
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
        $info->set(null);

        //since cache has the time appended to it, no reason to live for more than 60s
        $info->expiresAfter(60);

        // and saves it
        return $this->cache->save($info);
    }

    /**
     * @inheritdoc
     */
    public function unlock(JobInterface $job)
    {
        // TODO: Implement unlock() method.
    }
}
