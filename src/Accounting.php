<?php
/**
 * Created by PhpStorm.
 * User: alexchang
 * Date: 2019-02-16
 * Time: 15:20
 */

namespace App;


use Carbon\Carbon;

class Accounting
{

    /**
     * @var IBudgetRepo
     */
    private $budgetRepo;
    /**
     * @var Carbon
     */
    private $start;
    /**
     * @var Carbon
     */
    private $end;

    /**
     * Accounting constructor.
     * @param $budgetRepo
     */
    public function __construct(IBudgetRepo $budgetRepo)
    {
        $this->budgetRepo = $budgetRepo;
    }

    /**
     * @param Carbon $start
     * @param Carbon $end
     * @return float|int
     */
    public function totalAmount(Carbon $start, Carbon $end)
    {
//        new Period($start, $end);
        $this->start = $start;
        $this->end = $end;
        if ($this->invalidDate()) {
            return 0.00;
        }
        $totalBudget = 0;
        foreach ($this->budgetRepo->getAll() as $budget) {
            $budgetYearMonth = $budget->getBudgetYearMonth();
            if (!$this->isCrossMonth()) {
                return $budget->getAmount() * ($this->end->diffInDays($this->start) + 1) / $budgetYearMonth->daysInMonth;
            } else {
                if ($budgetYearMonth->isSameMonth($this->start)) {
                    $overlappingDays = $this->start->diffInDays($budgetYearMonth->endOfMonth()) + 1;
                    $totalBudget += $budget->getAmount() * $overlappingDays / $budgetYearMonth->daysInMonth;
                } else if ($budgetYearMonth->isSameMonth($this->end)) {
                    $overlappingDays = $budgetYearMonth->startOfMonth()->diffInDays($this->end) + 1;
                    $totalBudget += $budget->getAmount() * $overlappingDays / $budgetYearMonth->daysInMonth;
                } else if ($this->inRange($budgetYearMonth)) {
                    $totalBudget += $budget->getAmount();
                }
            }

        }
        return $totalBudget;
    }

    /**
     * @return bool
     */
    private function invalidDate(): bool
    {
        return $this->start->gt($this->end);
    }

    /**
     * @return mixed
     */
    private function isCrossMonth()
    {
        return !$this->start->isSameMonth($this->end);
    }

    /**
     * @param $budgetYearMonth
     * @return mixed
     */
    private function inRange($budgetYearMonth)
    {
        return $budgetYearMonth->between($this->start, $this->end);
    }
}