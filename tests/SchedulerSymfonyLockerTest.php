<?php


namespace Saloodo\Scheduler\Tests;


use DateInterval;
use DateTime;
use PHPUnit\Framework\TestCase;
use Saloodo\Scheduler\Jobs\JobExample;
use Saloodo\Scheduler\Jobs\Mutex\SchedulerSymfonyLocker;
use Symfony\Component\Lock\Store\FlockStore;

class SchedulerSymfonyLockerTest extends TestCase
{
    private $schedulerLocker;

    public function testCanLockSchedulerJobs()
    {
        $job = new JobExample();
        self::assertTrue($this->schedulerLocker->tryLock($job));
    }

    public function testCanUnlockSchedulerJobs()
    {
        $job = new JobExample();
        self::assertTrue($this->schedulerLocker->tryLock($job));
        self::assertTrue($this->schedulerLocker->unlock($job));
    }

    public function testCanNOTLockAlreadyLockedSchedulerJobs()
    {
        $job = new JobExample();
        self::assertTrue($this->schedulerLocker->tryLock($job));
        self::assertFalse($this->schedulerLocker->trylock($job));
    }

    public  function testCanLockSchedulerJobAfterUnLockIt(){
        $job = new JobExample();
        self::assertTrue($this->schedulerLocker->tryLock($job));
        self::assertTrue($this->schedulerLocker->unlock($job));
        self::assertTrue($this->schedulerLocker->tryLock($job));
    }

    public function testCanNotLockTheSameJobAtTheSameMin()
    {
        $job = new JobExample();
        $time = new DateTime(date("h:i"));

        self::assertTrue($this->schedulerLocker->tryLock($job, $time));

        $time->add(new DateInterval("PT59S"));

        self::assertFalse($this->schedulerLocker->tryLock($job,$time));
    }

    protected function setUp()
    {
        $this->schedulerLocker = new SchedulerSymfonyLocker(new FlockStore(__DIR__));
    }
}