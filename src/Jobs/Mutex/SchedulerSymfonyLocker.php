<?php


namespace Saloodo\Scheduler\Jobs\Mutex;


use DateTimeImmutable;
use DateTimeInterface;
use Saloodo\Scheduler\Contract\JobInterface;
use Saloodo\Scheduler\Contract\LockInterface;
use Symfony\Component\Lock\Factory;

class SchedulerSymfonyLocker implements  LockInterface
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
    public function tryLock(JobInterface $job, DateTimeInterface $time = null): bool
    {
        if (!$time) {
            $time = new DateTimeImmutable();
        }
        $key = $job->getUniqueId() . '_' . $time->format('Hi');

        $lock = $this->factory->createLock($key);

        return $lock->acquire();
    }

    /**
     * @inheritdoc
     */
    public function unlock(JobInterface $job)
    {
        // TODO: Implement unlock() method.
    }
}