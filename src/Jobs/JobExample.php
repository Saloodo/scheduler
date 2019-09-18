<?php


namespace Saloodo\Scheduler\Jobs;


class JobExample  extends  AbstractJob
{

    protected function initialize(Schedule $schedule)
    {
        $schedule->setTtl(100);
    }

    public function run()
    {
        //do something
    }

}