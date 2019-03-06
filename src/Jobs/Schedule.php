<?php

namespace Saloodo\Scheduler\Jobs;

use Cron\CronExpression;
use Cron\FieldFactory;


class Schedule
{
    private $cron;
    private $shouldRunOnOnlyOneServer = true;
    private $canOverlap = false;
    private $ttl = 60*30; // 30 minutes

    /**
     * Schedule constructor.
     * @param string $expression the default cron
     */
    public function __construct(string $expression = "* * * * *")
    {
        $this->cron = new CronExpression($expression, new FieldFactory());
    }

    /**
     * Set the cron to work at this day
     * @param int $day
     * @return $this
     */
    public function atDay(int $day): self
    {
        $this->cron->setPart(2, $day);
        return $this;
    }

    /**
     * Set the cron to work at this hour
     * @param int $hour
     * @return $this
     */
    public function atHour(int $hour): self
    {
        $this->cron->setPart(1, $hour);
        return $this;
    }

    /**
     * Set the cron to work at this minutes
     * @param int $minutes
     * @return $this
     */
    public function atMinute(int $minutes): self
    {
        $this->cron->setPart(0, $minutes);
        return $this;
    }

    /**
     * Set the cron to work at every x minutes
     * @param int $minutes
     * @return Schedule
     */
    public function everyMinutes($minutes = 1): self
    {
        return $this->spliceIntoPosition($minutes, 0);
    }

    /**
     * Set the cron to work at every x hours
     * @param int $hours
     * @return Schedule
     */
    public function everyHours($hours = 1): self
    {
        return $this->spliceIntoPosition($hours, 1);
    }

    public function everyMinute(): self
    {
        return $this->everyMinutes(1);
    }

    public function everyFiveMinutes(): self
    {
        return $this->everyMinutes(5);
    }

    public function everyTenMinutes(): self
    {
        return $this->everyMinutes(10);
    }

    public function everyFifteenMinutes(): self
    {
        return $this->everyMinutes(15);
    }

    public function everyThirtyMinutes(): self
    {
        return $this->everyMinutes(30);
    }

    /**
     * Sets the cron to work every hour
     * @return $this
     */
    public function hourly(): self
    {
        $this->cron->setPart(0, 0);
        return $this;
    }

    /**
     * Sets the cron to work every day
     * @return $this
     */
    public function daily(): self
    {
        $this->cron->setPart(2, "*");
        $this->cron->setPart(1, 0);
        $this->cron->setPart(0, 0);
        return $this;
    }

    /**
     * Sets the cron to work every month
     * @return $this
     */
    public function monthly(): self
    {
        $this->cron->setPart(3, "*");
        $this->cron->setPart(2, 1);
        $this->cron->setPart(1, 0);
        $this->cron->setPart(0, 0);
        return $this;
    }

    /**
     * @param int $value
     * @param int $position
     * @return $this
     */
    public function spliceIntoPosition($value, int $position): self
    {
        if ($value === 1) {
            $expr = "*";
        } else {
            $expr = "*/" . intval($value);
        }

        $this->cron->setPart($position, $expr);
        return $this;
    }

    /**
     * @return CronExpression
     */
    public function getCron(): CronExpression
    {
        return $this->cron;
    }

    /**
     * @return string
     */
    public function getExpression(): string
    {
        return $this->cron->getExpression();
    }

    public function setExpression(string $expression = "* * * * *"): self
    {
        $this->cron->setExpression($expression);
        return $this;
    }

    /**
     * Return true if the schedule is due to now
     * @param $currentTime
     * @return bool
     */
    public function isDue($currentTime = 'now'): bool
    {
        return $this->cron->isDue($currentTime);
    }

    /**
     * Sets whether the job should run on just one instance
     * @param bool $decision
     * @return $this
     */
    public function shouldRunOnOnlyOneInstance(bool $decision = true): self
    {
        $this->shouldRunOnOnlyOneServer = $decision;
        return $this;
    }

    /**
     * Sets whether the job can overlap
     * @param bool $decision
     * @return $this
     */
    public function canOverlap(bool $decision = true): self
    {
        $this->canOverlap = $decision;
        return $this;
    }

    /**
     * Sets the ttl to control job overlapping
     * @param int $ttl
     * @return $this
     */
    public function setTtl(int $ttl): self
    {
        $this->ttl = $ttl;
        return $this;
    }

    /**
     * @return int
     */
    public function getTtl(): int
    {
        return $this->ttl;
    }

    /** Check whether the job should run on just one instance
     * @return bool
     */
    public function checkShouldRunOnOnlyOneInstance(): bool
    {
        return $this->shouldRunOnOnlyOneServer;
    }

    /**
     * Check whether the job can overlap
     * @return bool
     */
    public function checkCanOverlap(): bool
    {
        return $this->canOverlap;
    }
}
