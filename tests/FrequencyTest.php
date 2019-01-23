<?php

namespace Saloodo\Scheduler\Tests;


use PHPUnit\Framework\TestCase;
use Saloodo\Scheduler\Jobs\Schedule;

class FrequencyTest extends TestCase
{
    public function testCanScheduleEvery5Minutes()
    {
        $schedule = new Schedule();

        $schedule->everyFiveMinutes();

        $this->assertEquals("*/5 * * * *", $schedule->getExpression());
    }

    public function testCanScheduleEvery10Minutes()
    {
        $schedule = new Schedule();

        $schedule->everyTenMinutes();

        $this->assertEquals("*/10 * * * *", $schedule->getExpression());
    }

    public function testCanScheduleEvery15Minutes()
    {
        $schedule = new Schedule();

        $schedule->everyFifteenMinutes();

        $this->assertEquals("*/15 * * * *", $schedule->getExpression());
    }

    public function testCanScheduleEvery30Minutes()
    {
        $schedule = new Schedule();

        $schedule->everyThirtyMinutes();

        $this->assertEquals("*/30 * * * *", $schedule->getExpression());
    }

    public function testCanScheduleEveryHour()
    {
        $schedule = new Schedule();

        $schedule->hourly();

        $this->assertEquals("0 * * * *", $schedule->getExpression());
    }

    public function testCanScheduleEveryDay()
    {
        $schedule = new Schedule();

        $schedule->daily();

        $this->assertEquals("0 0 * * *", $schedule->getExpression());
    }

    public function testCanScheduleEveryDayAt23()
    {
        $schedule = new Schedule();

        $schedule->daily()->atHour(23);

        $this->assertEquals("0 23 * * *", $schedule->getExpression());
    }

    public function testCanScheduleMonthly()
    {
        $schedule = new Schedule();

        //runs on the first
        $schedule->monthly();

        $this->assertEquals("0 0 1 * *", $schedule->getExpression());
    }

    public function testCanScheduleMonthlyOnThe10th()
    {
        $schedule = new Schedule();

        $schedule->monthly()->atDay(10);

        $this->assertEquals("0 0 10 * *", $schedule->getExpression());
    }

    public function testCanScheduleMonthlyOnThe10thAt16()
    {
        $schedule = new Schedule();

        $schedule->monthly()->atDay(10)->atHour(16);

        $this->assertEquals("0 16 10 * *", $schedule->getExpression());
    }
}
