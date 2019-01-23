# Saloodo Scheduler

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]
[![Build Status][ico-travis]][link-travis]

Saloodo Scheduler is a powerful job scheduler based on Laravel Scheduler.


## Instalation

``` bash
composer require saloodo/scheduler
```

Add the Bundle to AppKernel

```
 new Saloodo\Scheduler\SchedulerBundle(),
```


Define the cache configuration (used to avoid overlapping/run on just one instance):

```yaml
scheduler:
  cache_driver: 'app_general_cache'
```

Define the services, and tag them as `scheduler.job`

```yaml
 AppBundle\Jobs\JobEvery5Minute:
        arguments:
        tags:
            - { name: scheduler.job}
```

Create a Job class

>```php
>//AppBundle/Jobs/JobEvery5Minutes.php
><?php
>
>namespace AppBundle\Jobs;
>
>use Saloodo\Scheduler\Jobs\AbstractJob;
>use Saloodo\Scheduler\Jobs\Schedule;
>
>class JobEvery5Minutes extends AbstractJob
>{
>    protected function initialize(Schedule $schedule)
>    {
>        $schedule
>            ->everyFiveMinutes();
>    }
>
>    public function run()
>    {
>        //
>    }
>}
>```


Listen to the events

Saloodo Scheduler dispatches events out of the box. You can listen or subscribe to these events.

```
job.scheduler.started
job.scheduler.completed
job.started
job.completed
```

## Starting the scheduler
You only need to add the following Cron entry to your server.

```
* * * * * php /path-to-your-project/bin/console scheduler:run >> /dev/null 2>&1
```
## License

This package is open-sourced software licensed under the MIT license.

[ico-version]: https://img.shields.io/packagist/v/saloodo/scheduler.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/saloodo/scheduler.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/saloodo/scheduler/master.svg?style=flat-square


[link-packagist]: https://packagist.org/packages/saloodo/scheduler
[link-downloads]: https://packagist.org/packages/saloodo/scheduler
[link-travis]: https://travis-ci.org/saloodo/scheduler
[link-contributors]: ../../contributors]
