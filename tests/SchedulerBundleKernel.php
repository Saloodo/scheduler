<?php


namespace Saloodo\Scheduler\Tests;


use Saloodo\Scheduler\SchedulerBundle;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Lock\Store\FlockStore;

class SchedulerBundleKernel extends Kernel
{

    private $saloodoSchedulerConfig;

    public function __construct(array $config = [])
    {
        $this->saloodoSchedulerConfig = $config;
        parent::__construct('test', true);
    }

    public function registerBundles()
    {
        return [
            new SchedulerBundle()
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(function (ContainerBuilder $container) {
            $container->register('test_cache_driver', FilesystemAdapter::class);
            $container->register('test_cache_store', FlockStore::class);
            $container->register('event_dispatcher', EventDispatcher::class);
            $container->loadFromExtension('scheduler', $this->saloodoSchedulerConfig);
        });
    }
}