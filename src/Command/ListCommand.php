<?php

namespace Saloodo\Scheduler\Command;

use Saloodo\Scheduler\Contract\JobInterface;
use Saloodo\Scheduler\Jobs\Scheduler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ListCommand extends Command
{
    /** @var ContainerInterface */
    private $container;

    /**
     * ListCommand constructor.
     * @param ContainerInterface $container
     * @param string|null $name
     */
    public function __construct(ContainerInterface $container, string $name = null)
    {
        $this->container = $container;

        parent::__construct($name);
    }


    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName("jobs:list")
            ->setDescription("List the existing jobs")
            ->setHelp("This command display the list of registered jobs.");
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $scheduler = $this->container->get(Scheduler::class);
        $table = new Table($output);
        $table->setHeaders([
            "ID",
            "Class",
            "Expression",
            "Next Execution",
            "Can overlap",
            "Run only on one instance",
        ]);

        $id = 1;
        foreach ($scheduler->getJobs() as $job) {
            /** @var JobInterface $job */
            $table->addRow([
                $job->getUniqueId(),
                get_class($job),
                $job->getSchedule()->getExpression(),
                $job->getSchedule()->getNextRunDate()->format('d-m-y H:i:s'),
                $job->getSchedule()->checkCanOverlap() ? 'yes' : 'no',
                $job->getSchedule()->checkShouldRunOnOnlyOneInstance() ? 'yes' : 'no',
            ]);
        };

        $table->render();
    }
}
