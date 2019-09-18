<?php

namespace Saloodo\Scheduler\Jobs\Mutex;

use Saloodo\Scheduler\Contract\JobInterface;
use Saloodo\Scheduler\Contract\LockInterface;
use Symfony\Component\Lock\Factory;
use Symfony\Component\Lock\Key;
use Symfony\Component\Lock\StoreInterface;


class JobSymfonyLocker implements LockInterface
{
    /** @var Factory $factory */
    protected  $factory;
    /**
     * @var StoreInterface
     */
    protected  $lockStore;

    public function __construct(StoreInterface $store)
    {
        $this->lockStore = $store;
        $this->factory = new Factory($store);
    }

    /**
     * @inheritdoc
     */
    public function tryLock(JobInterface $job): bool
    {
        /**
         * Lock $lock
         */
        $lock = $this->factory->createLock(
            $job->getUniqueId(),
            $job->getSchedule()->getTtl(),
            false);

        if($lock->acquire()){
            $job->setLock($lock);
            return true;
        }
       return false;
    }

    /**
     * @inheritdoc
     */
    public function unlock(JobInterface $job): bool
    {
        if(is_null($job->getLock())){
            return true;
        }
       return $job->getLock()->release() == null;
    }
}
