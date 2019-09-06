<?php

namespace Saloodo\Scheduler\DependencyInjection;

use Saloodo\Scheduler\Jobs\Mutex\JobLocker;
use Saloodo\Scheduler\Jobs\Mutex\JobSymfonyLocker;
use Saloodo\Scheduler\Jobs\Mutex\SchedulerLocker;
use Saloodo\Scheduler\Jobs\Mutex\SchedulerSymfonyLocker;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class SchedulerExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $definition = $container->getDefinition(SchedulerLocker::class);

        $definition->replaceArgument(0, new Reference($config['cache_driver']));

        $definition = $container->getDefinition(JobLocker::class);
        $definition->replaceArgument(0, new Reference($config['cache_driver']));

        $definition = $container->getDefinition(SchedulerSymfonyLocker::class);
        $definition->replaceArgument(0, new Reference( $config['cache_store']));

        $definition = $container->getDefinition(JobSymfonyLocker::class);
        $definition->replaceArgument(0, new Reference( $config['cache_store']));
    }
}
