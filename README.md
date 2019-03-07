# Saloodo Scheduler

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]
[![Build Status][ico-travis]][link-travis]

Saloodo Scheduler is a powerful job scheduler inspired on Laravel Scheduler.
It runs jobs asynchronously using a different php process for each job.


## Instalation

### Require the package

``` bash
composer require saloodo/scheduler
```

### Add the Bundle to AppKernel

```
 new Saloodo\Scheduler\SchedulerBundle(),
```

### Define the cache configuration

```yaml
scheduler:
  cache_driver: 'app_general_cache'
```

## How do I add jobs to the scheduler?

### Create a Job class

>```php
>//AppBundle/Jobs/ExpireShipments.php
><?php
>
>namespace AppBundle\Jobs;
>
>use Saloodo\Scheduler\Jobs\AbstractJob;
>use Saloodo\Scheduler\Jobs\Schedule;
>
>class ExpireShipments extends AbstractJob
>{
>    private $repository;
>   
>    public function __construct(ShipmentRepository $repository)
>    {
>       $this->repository = $repository;
>    }
>     
>    protected function initialize(Schedule $schedule)
>    {
>        //sets the execution of this job to every 5 minutes
>        $schedule
>            ->everyFiveMinutes();
>    }
>
>    public function run()
>    {
>        $this->repository->expireShipments();
>    }
>}
>```

### Add your created job to services


```yaml
 AppBundle\Jobs\ExpireShipments:
        arguments:
            - '@App/Repository/ShipmentRepository'
        #Tag them as scheduler.job
        tags:
            - { name: scheduler.job}
```



### Listen to the events

Saloodo Scheduler dispatches events out of the box. You can listen or subscribe to these events.

```
job.scheduler.started
job.scheduler.completed
job.started
job.completed
job.failed
job.skipped
```

## Starting the scheduler
You only need to add the following Cron entry to your server.

```bash
* * * * * php /path-to-your-project/bin/console jobs:run >> /dev/null 2>&1

```

## Options

### Run on just one server
```php
$scheduler->shouldRunOnOnlyOneInstance();

```

By default, jobs will be executed on only one instance. It means that if more than one instance triggers the scheduler execution at the same minute, jobs it will skipped. To overwrite this setting and allow multiple execution of the same job on the same minute, use `$scheduler->shouldRunOnOnlyOneInstance(false);` 

### Job overlapping
```php
$scheduler->canOverlap(false);

```

By default, jobs cannot be overlapped. Before it starts, a job check if it's already running. If jobs can overlap, overwrite this setting by using `$scheduler->canOverlap(true);`.


NOTE: When canOverlap is set to false, you can also set a ttl for the job locker. `->setTttl(60) //number in seconds`.
This is particularly important to avoid locking a task for unnecessary periods of time on a unexpected system shutdown that could wrongly keep the task locked.

this is the  MAX time that the lock would be held. In normal conditions, after the job execution the lock is automatically released.


### Job frequency

```php
$scheduler->everyMinute(); // runs job every minute

$scheduler->everyFiveMinutes(); // runs job every 5 minutes

$scheduler->everyTenMinutes(); // runs job every 10 minutes

$scheduler->everyFifteenMinutes(); // runs job every 15 minutes

$scheduler->everyMinutes(23); // runs job every 23 minutes

$scheduler->daily()->atHour(17)->atMinute(30); // runs job every day at 17:30

$scheduler->hourly(); // runs job every hour

$scheduler->daily(); // runs job every day at 00:00

$scheduler->daily()->atHour(17)->atMinute(30); // runs job every day at 17:30

$scheduler->monthly()->atDay(3);  // runs job month, at the 3rd at 00:00

$scheduler->monthly()->atDay(3)->atHour(17)->atMinute(30); // runs job month, at the 3rd at 17:30

$scheduler->setExpression("* 11,17 * * *"); // sets raw  expression, runs job every day at 11:00 and 17:00
```

### Using all together
```php
$scheduler->canOverlap(true)->shouldRunOnOnlyOneInstance(false)->everyFiveMinutes();

```

## Commands

### Running a single job
You can manually trigger a single job execution by simply executing `bin/console jobs:run {id}` or  `bin/console jobs:run {fullyQualifiedClassName}` from you application root.

Single jobs will always be executed, as the check for overlapping and running on single server are done by the "run multiple jobs command")

### Running all jobs
To mannually trigger all jobs, simply run `bin/console jobs:run`. To force execution of all due jobs (without checking for overlap or running on single server), optionally, pass a `--force` argument. This will skip the check for running on a single server.

### Listing jobs

To get an overview of the defined jobs, run `bin/console jobs:list`.

 ```
 +------------+-----------------------------------------------------+--------------+-------------+--------------------------+
 | ID         | Class                                               | Expression   | Can overlap | Run only on one instance |
 +------------+-----------------------------------------------------+--------------+-------------+--------------------------+
 | da6f3a6948 | AppBundle\Jobs\JobEvery1Minute                      | * * * * *    | no          | yes                      |
 | 26e25dbd48 | AppBundle\Jobs\JobEvery1MinuteThatOverlaps          | * * * * *    | yes         | yes                      |
 | f9dfbec59a | AppBundle\Jobs\JobEvery5Minutes                     | */5 * * * *  | no          | yes                      |
 | d2f87097db | AppBundle\Jobs\JobEvery10MinutesRunningInAllServers | */10 * * * * | no          | no                       |
 +------------+-----------------------------------------------------+--------------+-------------+--------------------------+

```
## License

This package is open-sourced software licensed under the MIT license.

[ico-version]: https://img.shields.io/packagist/v/saloodo/scheduler.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/saloodo/scheduler.svg?style=flat-square
[ico-travis]: https://api.travis-ci.com/Saloodo/scheduler.svg?branch=master


[link-packagist]: https://packagist.org/packages/saloodo/scheduler
[link-downloads]: https://packagist.org/packages/saloodo/scheduler
[link-travis]: https://travis-ci.org/saloodo/scheduler
[link-contributors]: ../../contributors]
