<?php


namespace Saloodo\Scheduler\Tests\Functional;


use PHPUnit\Framework\TestCase;
use Saloodo\Scheduler\Jobs\Mutex\JobLocker;
use Saloodo\Scheduler\Jobs\Mutex\JobSymfonyLocker;
use Saloodo\Scheduler\Jobs\Mutex\SchedulerLocker;
use Saloodo\Scheduler\Jobs\Mutex\SchedulerSymfonyLocker;
use Saloodo\Scheduler\Jobs\Scheduler;
use Saloodo\Scheduler\Tests\SchedulerBundleKernel;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ServiceWiringTest extends TestCase
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function testSchedulerLockerServiceWiring(): void
    {
        /** @var SchedulerLocker $schedulerLockerService */
        $schedulerLockerService = $this->container->get(SchedulerLocker::class);

        self::assertInstanceOf(SchedulerLocker::class, $schedulerLockerService);
    }

    public function testSchedulerSymfonyLockerWiring(): void
    {
        /** @var SchedulerSymfonyLocker $schedulerSymfonyLockerService */
        $schedulerSymfonyLockerService = $this->container->get(SchedulerSymfonyLocker::class);

        self::assertInstanceOf(SchedulerSymfonyLocker::class, $schedulerSymfonyLockerService);
    }

    public function testJobLockerWiring(): void
    {
        /** @var JobLocker $jobLocker */
        $jobLocker = $this->container->get(JobLocker::class);

        self::assertInstanceOf(JobLocker::class, $jobLocker);
    }

    public function testJobSymfonyLockerWiring(): void
    {
        /** @var JobLocker $jobSymfonyLocker */
        $jobSymfonyLocker = $this->container->get(JobSymfonyLocker::class);

        self::assertInstanceOf(JobSymfonyLocker::class, $jobSymfonyLocker);
    }

    public function testSchedulerWiring(): void
    {
        /** @var Scheduler $scheduler */
        $scheduler = $this->container->get(Scheduler::class);

        self::assertInstanceOf(Scheduler::class, $scheduler);
    }

    protected function setUp(): void
    {
        $config = [
            'cache_driver' => 'test_cache_driver',
            'cache_store' => 'test_cache_store'
        ];
        $kernel = new SchedulerBundleKernel($config);
        $kernel->boot();
        $this->container = $kernel->getContainer();
    }
}