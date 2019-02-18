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
        $period = new Period($start, $end);
        if ($period->start()->gt($period->end())) {
            return 0.00;
        }
        $totalBudget = 0;
        foreach ($this->budgetRepo->getAll() as $budget) {
            if ($period->isCrossMonth()) {
                if ($budget->start()->isAfter($period->end())) {
                    $totalBudget += 0;
                } else if ($budget->end()->isBefore($period->start())) {
                    $totalBudget += 0;
                } else if ($budget->start()->isBefore($period->start())) {
                    $overlappingStart = $period->start();
                    $overlappingEnd = $period->end();
//                    $totalBudget += $budget->dailyAmount() * ($overlappingEnd->diffInDays($overlappingStart));
                    $overlappingDays = $budget->end()->diffInDays($period->start()) + 1;
                    $totalBudget += $budget->dailyAmount() * $overlappingDays;
                } else if ($budget->end()->isAfter($period->end())) {
                    $overlappingStart = $budget->start();
                    $overlappingEnd = $period->end();
                    $totalBudget += $budget->dailyAmount() * ($overlappingEnd->diffInDays($overlappingStart) + 1);
//                    $overlappingDays = $budget->start()->diffInDays($period->end()) + 1;
//                    $totalBudget += $budget->dailyAmount() * $overlappingDays;
                } else if ($budget->start()->isAfter($period->start()) && $budget->end()->isBefore($period->end())) {
                    $overlappingStart = $budget->start();
                    $overlappingEnd = $budget->end();
                    $totalBudget += $budget->dailyAmount() * ($overlappingEnd->diffInDays($overlappingStart) + 1);
//                    $overlappingDays = $budget->yearMonth()->daysInMonth;
//                    $totalBudget += $budget->dailyAmount() * $overlappingDays;
                } else {
                    $totalBudget += 0;
                }
            } else {
                $overlappingDays = $period->days();
                $totalBudget += $budget->dailyAmount() * $overlappingDays;
            }
        }
        return $totalBudget;
    }

}