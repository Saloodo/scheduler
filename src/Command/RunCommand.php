<?php

namespace Saloodo\Scheduler\Command;

use Saloodo\Scheduler\Contract\JobInterface;
use Saloodo\Scheduler\Event\SchedulerCompletedEvent;
use Saloodo\Scheduler\Event\SchedulerStartedEvent;
use Saloodo\Scheduler\Jobs\Scheduler;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
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
    protected function configure()
    {
        $this
            ->setName("jobs:run")
            ->setDescription("Run due jobs")
            ->addArgument("id", InputArgument::OPTIONAL, "The ID of the task.");
    }

    /**
     * @inheritdoc
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->scheduler = $scheduler = $this->getContainer()->get(Scheduler::class);
        $this->eventDispatcher = $scheduler = $this->getContainer()->get('event_dispatcher');
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

        $this->runJobs();
    }

    /**
     * Run all jobs
     * @param callable|null $callable A callback to receive all proccess when they are finished
     */
    protected function runJobs(Callable $callable = null)
    {
        $this->eventDispatcher->dispatch(SchedulerStartedEvent::NAME, new SchedulerStartedEvent());

        $phpBinaryFinder = new PhpExecutableFinder();
        $phpBinaryPath = $phpBinaryFinder->find();

        $symfonyPath = $this->getApplication()->getKernel()->getProjectDir();

        $allProcesses = [];

        /** @var JobInterface $job */
        foreach ($this->scheduler->getDueJobs("now") as $job) {

            echo get_class($job) . PHP_EOL;

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

            $allProcesses[] = $process;
        }

        $this->waitForProcesses($allProcesses);

        $this->eventDispatcher->dispatch(SchedulerCompletedEvent::NAME, new SchedulerCompletedEvent());

        $return = $allProcesses;

        if (is_callable($callable)) {
            call_user_func($callable, $return);
        }
    }

    /**
     * Executes a single Job
     * @param string $id
     */
    protected function runSingleJob(string $id)
    {
        /** @var JobInterface $job */
        foreach ($this->scheduler->getDueJobs("now") as $job) {
            if ($job->getUniqueId() == $id) {

                if ($job->getSchedule()->checkShouldRunOnOnlyOneInstance()) {
                    $this->scheduler->runSingleServerJob($job);
                } else {
                    $this->scheduler->runJob($job);
                }
            }
        }
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
