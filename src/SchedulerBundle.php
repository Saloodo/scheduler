<?php

namespace Saloodo\Scheduler;

use Saloodo\Scheduler\DependencyInjection\JobPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SchedulerBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new JobPass());
    }
}
