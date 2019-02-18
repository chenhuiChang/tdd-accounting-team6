<?php


namespace App;


use Carbon\Carbon;

class Period
{
    /**
     * @var Carbon
     */
    private $start;
    /**
     * @var Carbon
     */
    private $end;

    /**
     * Period constructor.
     * @param Carbon $start
     * @param Carbon $end
     */
    public function __construct(\Carbon\Carbon $start, \Carbon\Carbon $end)
    {
        $this->start = $start;
        $this->end = $end;
    }

    public function start()
    {
        return $this->start;
    }

    public function end()
    {
        return $this->end;
    }

    /**
     * @return bool
     */
    public function isCrossMonth(): bool
    {
        return !$this->start->isSameMonth($this->end);
    }

    /**
     * @return int
     */
    public function days(): int
    {
        return $this->end->diffInDays($this->start)+1;
    }
}