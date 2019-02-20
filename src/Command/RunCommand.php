<?php

namespace Saloodo\Scheduler\Command;

use Saloodo\Scheduler\Contract\JobInterface;
use Saloodo\Scheduler\Event\JobSkippedEvent;
use Saloodo\Scheduler\Event\SchedulerCompletedEvent;
use Saloodo\Scheduler\Event\SchedulerStartedEvent;
use Saloodo\Scheduler\Jobs\Scheduler;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

class RunCommand extends ContainerAwareCommand
{
    /** @var Scheduler */
    private $scheduler;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /** @var string */
    private $environment;

    /**
     * @inheritdoc
     */
    public function __construct(Scheduler $scheduler, EventDispatcherInterface $eventDispatcher, ?string $name = null)
    {
        $this->scheduler = $scheduler;
        $this->eventDispatcher = $eventDispatcher;

        parent::__construct($name);
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName("jobs:run")
            ->setDescription("Run due jobs")
            ->addArgument("id", InputArgument::OPTIONAL, "The ID of the task.")
            ->addOption('force', null, InputOption::VALUE_OPTIONAL, 'Whether execution of all jobs should be forced', false);
    }

    /**
     * @inheritdoc
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->environment = $this->getContainer()->getParameter('kernel.environment');
        parent::initialize($input, $output);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $id = $input->getArgument("id");

        if ($id) {
            $this->runSingleJob($id);
            return;
        }

        $this->runJobs($input->getOption('force') !== false);

        $memoryUsage = memory_get_usage(true) / 1024 / 1024;
        $output->writeln("Scheduler finished with memory: ${memoryUsage}M");
    }

    /**
     * Run all jobs
     * @param bool $force Whether all jobs execution should be forced
     * @param callable|null $callable A callback to receive all process when they are finished
     */
    protected function runJobs(bool $force, Callable $callable = null)
    {
        $this->eventDispatcher->dispatch(SchedulerStartedEvent::NAME, new SchedulerStartedEvent());

        $allProcesses = [];

        /** @var JobInterface $job */
        foreach ($this->scheduler->getDueJobs("now") as $job) {
            if ($this->shouldRun($job) || $force) {
                $allProcesses[] = $this->createProcess($job);
            }
        }

        $this->waitForProcesses($allProcesses);

        $this->eventDispatcher->dispatch(SchedulerCompletedEvent::NAME, new SchedulerCompletedEvent());

        if (is_callable($callable)) {
            call_user_func($callable, $allProcesses);
        }
    }

    private function shouldRun(JobInterface $job): bool
    {
        if ($job->getSchedule()->checkShouldRunOnOnlyOneInstance()) {
            if (!$this->scheduler->serverShouldRun($job)) {
                $this->eventDispatcher->dispatch(JobSkippedEvent::NAME, new JobSkippedEvent($job, JobSkippedEvent::SERVER_SHOULD_NOT_RUN));
                return false;
            }
        }

        if (!$job->getSchedule()->checkCanOverlap()) {
            if ($this->scheduler->wouldOverlap($job)) {
                $this->eventDispatcher->dispatch(JobSkippedEvent::NAME, new JobSkippedEvent($job, JobSkippedEvent::WOULD_OVERLAP));
                return false;
            }
        }

        return true;
    }

    private function createProcess(JobInterface $job): Process
    {
        $phpBinaryFinder = new PhpExecutableFinder();
        $phpBinaryPath = $phpBinaryFinder->find();
        $symfonyPath = $this->getApplication()->getKernel()->getProjectDir();

        $process = new Process(
            sprintf(
                '%s %s/bin/console jobs:run %s --env=%s',
                $phpBinaryPath,
                $symfonyPath,
                $job->getUniqueId(),
                $this->environment)
        );

        $process->setTimeout(null);

        $process->start(function ($a, $b) {
            echo $b;
        });

        return $process;
    }

    /**
     * Executes a single Job by a single id or job name
     * @param string $id
     * @return bool
     */
    protected function runSingleJob(string $id): bool
    {
        /** @var JobInterface $job */
        $jobs = array_filter($this->scheduler->getJobs(), function (JobInterface $item) use ($id) {
            return $item->getUniqueId() === $id || $item->getName() === $id;
        });

        $job = reset($jobs);

        if ($job === false) {
            return false;
        }

        // runs job on single server
        $this->scheduler->run($job);

        return true;
    }

    /**
     * Waits until all processes are finished
     * @param array $allProcesses
     */
    protected function waitForProcesses(array $allProcesses)
    {
        /** @var JobInterface $job */
        foreach ($allProcesses as $process) {
            $process->wait();

            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }
        }
    }
}
