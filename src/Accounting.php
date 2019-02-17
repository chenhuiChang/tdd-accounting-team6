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
            if ($this->isCrossMonth($start, $end)) {
                if ($budgetYearMonth->isSameMonth($start)) {
                    $overlappingDays = $start->diffInDays($budgetYearMonth->endOfMonth()) + 1;
                } else if ($budgetYearMonth->isSameMonth($end)) {
                    $overlappingDays = $budgetYearMonth->startOfMonth()->diffInDays($end) + 1;
                } else if ($budgetYearMonth->between($start, $end)) {
                    $overlappingDays = $budgetYearMonth->daysInMonth;
                } else {
                    $overlappingDays = 0;
                }
            } else {
                $overlappingDays = $end->diffInDays($start) + 1;
            }
            $totalBudget += $budget->getDailyAmount() * $overlappingDays;
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

}