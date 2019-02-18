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
        $totalAmount = 0;
        if ($period->isSameMonth()) {
            foreach ($this->budgetRepo->getAll() as $budget) {
                if ($budget->start()->isSameMonth($period->start())) {
                    $totalAmount += $budget->dailyAmount() * $period->days();
                }
            }
            return $totalAmount;
        } else {
            foreach ($this->budgetRepo->getAll() as $budget) {
                if ($budget->start()->isAfter($period->end()) || $budget->end()->isBefore($period->start())) {
                    $totalAmount += 0;
                } else if ($budget->start()->isBefore($period->start())) {
                    $overlappingDays = $budget->days() - $period->start()->day + 1;
                    $totalAmount += $budget->dailyAmount() * $overlappingDays;
//                    $totalAmount += $budget->dailyAmount() * ($overlappingEnd->diffInDays($overlappingStart));
//                    $overlappingDays = $budget->end()->diffInDays($period->start()) + 1;
//                    $totalAmount += $budget->dailyAmount() * $overlappingDays;
                } else if ($budget->end()->isAfter($period->end())) {
                    $overlappingStart = $budget->start()->day;
                    $overlappingEnd = $period->end()->day;
                    $totalAmount += $budget->dailyAmount() * ($overlappingEnd - $overlappingStart + 1);
                } else if ($budget->start()->isAfter($period->start()) && $budget->end()->isBefore($period->end())) {
                    $overlappingStart = $budget->start();
                    $overlappingEnd = $budget->end();
                    $totalAmount += $budget->dailyAmount() * ($overlappingEnd->diffInDays($overlappingStart) + 1);
                } else {
                    $totalAmount += 0;
                }
            }
        }
        return $totalAmount;
    }

}