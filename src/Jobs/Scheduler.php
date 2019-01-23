<?php

namespace Saloodo\Scheduler\Jobs;

use Saloodo\Scheduler\Contract\JobInterface;
use Saloodo\Scheduler\Event\JobCompletedEvent;
use Saloodo\Scheduler\Event\JobFailedEvent;
use Saloodo\Scheduler\Event\JobSkippedEvent;
use Saloodo\Scheduler\Event\JobStartedEvent;
use Saloodo\Scheduler\Jobs\Mutex\JobLocker;
use Saloodo\Scheduler\Jobs\Mutex\SchedulerLocker;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Scheduler
{
    /** @var EventDispatcher */
    private $dispatcher;

    /** @var SchedulerLocker */
    private $schedulerLocker;

    /** @var JobLocker */
    private $jobLocker;

    /** @var JobInterface[] */
    private $jobs = [];

    public function __construct(EventDispatcherInterface $dispatcher = null, SchedulerLocker $schedulerLocker, JobLocker $jobLocker)
    {
        $this->dispatcher = $dispatcher;
        $this->schedulerLocker = $schedulerLocker;
        $this->jobLocker = $jobLocker;
    }

    /**
     * Adds the job to the job stack
     * @param JobInterface $job
     */
    public function addJob(JobInterface $job)
    {
        $this->jobs[] = $job;
    }

    /**
     * Run the job if the specific instance should run
     * @param JobInterface $job
     */
    public function runSingleServerJob(JobInterface $job)
    {
        if ($this->serverShouldRun($job)) {

            $this->runJob($job);
            return;
        }

        $this->dispatcher->dispatch(JobSkippedEvent::NAME, new JobSkippedEvent($job, JobSkippedEvent::SERVER_SHOULD_NOT_RUN));
    }

    /**
     * Call to run job, before checking for overlapping and server run
     * @param JobInterface $job
     */
    public function runJob(JobInterface $job)
    {
        if ($job->getSchedule()->checkCanOverlap()) {
            $this->finallyRun($job);
            return;
        }

        $this->runWithoutOverlapping($job);
    }

    /**
     * Run the job if task would not overlap
     * @param JobInterface $job
     */
    protected function runWithoutOverlapping(JobInterface $job)
    {
        if ($this->wouldOverlap($job)) {
            $this->dispatcher->dispatch(JobSkippedEvent::NAME, new JobSkippedEvent($job, JobSkippedEvent::WOULD_OVERLAP));
            
            return;
        }

        $this->finallyRun($job);
    }

    /**
     * Finally tun the jobs
     * @param JobInterface $job
     */
    protected function finallyRun(JobInterface $job)
    {
        $this->dispatcher->dispatch(JobStartedEvent::NAME, new JobStartedEvent($job));

        try {
            $job->run();
        } catch (\Throwable $e) {
            $this->dispatcher->dispatch(JobFailedEvent::NAME, new JobFailedEvent($job, $e));
        } finally {
            // always tries to delete the cache, even if job was not locked.
            $this->jobLocker->unlock($job);
        }

        $this->dispatcher->dispatch(JobCompletedEvent::NAME, new JobCompletedEvent($job));
    }

    /**
     * Whether the attempt to run would overlap. It creates the lock if it would not overlap
     * @param JobInterface $job
     * @return bool
     */
    protected function wouldOverlap(JobInterface $job): bool
    {
        //if cannot lock, it means that the key exists already, and it would overlap
        return !$this->jobLocker->tryLock($job);
    }

    /**
     * Whether the scheduler should run on this specific instance
     * @param JobInterface $job
     * @return bool
     */
    protected function serverShouldRun(JobInterface $job): bool
    {
        return $this->schedulerLocker->tryLock($job);
    }

    /**
     * Return the jobs
     * @return JobInterface[]
     */
    public function getJobs(): array
    {
        return $this->jobs;
    }

    /**
     * Return the due jobs for the given time
     * @param $currentTime
     * @return array
     */
    public function getDueJobs($currentTime): array
    {
        $dueJobs = [];

        foreach ($this->getJobs() as $job) {
            if ($job->isDue($currentTime)) {
                $dueJobs[] = $job;
            }
        }

        return $dueJobs;
    }
}
