<?php
/**
 * Created by PhpStorm.
 * User: alexchang
 * Date: 2019-02-16
 * Time: 15:20
 */

namespace App;


use Carbon\Carbon;
use function substr;

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
        if ($start->gt($end)) {
            return 0.00;
        }
        $totalBudget = 0;
        foreach ($this->budgetRepo->getAll() as $budget) {
            $budgetYearMonth = $budget->getBudgetYearMonth();
            $budgetDays = $this->getBudgetDays($budget);
            if (!$this->isCrossMonth($start, $end)) {
                return $budget->getAmount() * ($end->diffInDays($start) + 1) / $budgetDays;
            } else {
                if ($budgetYearMonth->isSameMonth($start)) {
                    $overlappingDays = $start->diffInDays($budgetYearMonth->endOfMonth()) + 1;
                    $totalBudget += $budget->getAmount() * $overlappingDays / $budgetDays;
                } else if ($budgetYearMonth->isSameMonth($end)) {
                    $overlappingDays = $budgetYearMonth->startOfMonth()->diffInDays($end) + 1;
                    $totalBudget += $budget->getAmount() * $overlappingDays / $budgetDays;
                } else {
                    if ($budgetYearMonth->between($start, $end)) {
                        $totalBudget += $budget->getAmount();
                    }
                }
            }

        }
        return $totalBudget;
    }

    /**
     * @param Carbon $start
     * @param Carbon $end
     * @return mixed
     */
    private function isCrossMonth($start, $end)
    {
        return !$start->isSameMonth($end);
    }

    /**
     * @param Budget $budget
     * @return int
     */
    private function getBudgetDays(Budget $budget): int
    {
        $budgetDays = $budget->getBudgetYearMonth()->daysInMonth;
        return $budgetDays;
    }

}