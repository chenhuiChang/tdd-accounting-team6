<?php
/**
 * Created by PhpStorm.
 * User: alexchang
 * Date: 2019-02-16
 * Time: 15:42
 */

namespace App;


use Carbon\Carbon;

/**
 * Class Budget
 * @package App
 */
class Budget
{
    private $yearMonth;
    private $amount;

    /**
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * Budget constructor.
     * @param string $date
     * @param float $amount
     */
    public function __construct(string $date, float $amount)
    {
        $this->yearMonth = $date;
        $this->amount = $amount;
    }

    /**
     * @return Carbon|\Carbon\CarbonInterface
     */
    public function getBudgetYearMonth()
    {
        return Carbon::create(substr($this->yearMonth, 0, 4), substr($this->yearMonth, 4, 2));
    }

    /**
     * @return int
     */
    public function getBudgetDays(): int
    {
        return $this->getBudgetYearMonth()->daysInMonth;
    }
}