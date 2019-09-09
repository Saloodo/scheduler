<?php

namespace Saloodo\Scheduler\Jobs\Mutex;

use Saloodo\Scheduler\Contract\JobInterface;
use Saloodo\Scheduler\Contract\LockInterface;
use Symfony\Component\Lock\Factory;
use Symfony\Component\Lock\StoreInterface;


class JobSymfonyLocker implements LockInterface
{
    /** @var Factory $factory */
    protected  $factory;

    public function __construct(StoreInterface $store)
    {
        $this->factory = new Factory($store);
    }

    /**
     * @inheritdoc
     */
    public function tryLock(JobInterface $job): bool
    {
        $lock = $this->factory->createLock($job->getUniqueId(),$job->getSchedule()->getTtl());
        return  $lock->acquire();
    }

    /**
     * @inheritdoc
     */
    public function unlock(JobInterface $job): bool
    {
        $lock = $this->factory->createLock($job->getUniqueId());
        return $lock->release() == null;
    }
}
