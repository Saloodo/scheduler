<?php


namespace Saloodo\Scheduler\Jobs\Mutex;


use DateTimeImmutable;
use DateTimeInterface;
use Saloodo\Scheduler\Contract\JobInterface;
use Saloodo\Scheduler\Contract\LockInterface;
use Symfony\Component\Lock\Factory;
use Symfony\Component\Lock\StoreInterface;

class SchedulerSymfonyLocker implements LockInterface
{
    /** @var Factory $factory */
    protected $factory;

    public function __construct(StoreInterface $store)
    {
        $this->factory = new Factory($store);
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

        $lock = $this->factory->createLock(
            $key,
            $job->getSchedule()->getTtl(),
            false);

        if ($lock->acquire()) {
            $job->setLock($lock);
            return true;
        }
        return false;
    }

    /**
     * @inheritdoc
     */
    public function unlock(JobInterface $job)
    {
       return $job->getLock()->release() == null;
    }
}