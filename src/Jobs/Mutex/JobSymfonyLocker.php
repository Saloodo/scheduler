<?php

namespace Saloodo\Scheduler\Jobs\Mutex;

use Saloodo\Scheduler\Contract\JobInterface;
use Saloodo\Scheduler\Contract\LockInterface;
use Symfony\Component\Lock\Factory;


class JobSymfonyLocker implements LockInterface
{
    /** @var Factory $factory */
    protected  $factory;

    public function __construct(Factory $factory)
    {
        $this->factory = $factory;
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
        return $lock->release();
    }
}
