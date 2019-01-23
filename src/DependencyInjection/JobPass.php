<?php

namespace Saloodo\Scheduler\DependencyInjection;

use Saloodo\Scheduler\Jobs\Scheduler;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class JobPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        //Adds services tagged with "job" to the scheduler

        $scheduler = $container->findDefinition(Scheduler::class);
        $jobs = $container->findTaggedServiceIds('scheduler.job');

        foreach ($jobs as $id => $tags) {
            $scheduler->addMethodCall("addJob", [new Reference($id)]);
        }
    }
}
