<?php


namespace Saloodo\Scheduler\Tests;


use PHPUnit\Framework\TestCase;
use Saloodo\Scheduler\Jobs\JobExample;
use Saloodo\Scheduler\Jobs\Mutex\JobSymfonyLocker;
use Symfony\Component\Lock\Store\FlockStore;

class JobSymfonyLockerTest extends TestCase
{

    private $jobLocker;

    public function testCanLockJob()
    {
        $job = new JobExample();
        self::assertTrue($this->jobLocker->tryLock($job));
    }

    public function testCanUnlockJob()
    {
        $job = new JobExample();
        $this->jobLocker->tryLock($job);
        self::assertTrue($this->jobLocker->unlock($job));
    }

    public function testCanNOTLockAlreadyLockedJob()
    {
        $job = new JobExample();

        self::assertTrue($this->jobLocker->tryLock($job));
        self::assertFalse($this->jobLocker->tryLock($job));
    }

    public function testCanLockJobAfterUnlock()
    {
        $job = new JobExample();

        self::assertTrue($this->jobLocker->tryLock($job));
        self::assertTrue($this->jobLocker->unlock($job));
        self::asserttrue($this->jobLocker->tryLock($job));
    }

    protected function setUp()
    {
      //  $cache = new RedisStore(new Client());
        $this->jobLocker = new JobSymfonyLocker(new FlockStore(__DIR__));
    }
}