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
                    $overlappingStart = $period->start()->day;
                    $overlappingEnd = $period->end()->day;
                    $overlappingDays = $overlappingEnd - $overlappingStart + 1;
                    $totalAmount += $budget->dailyAmount() * $overlappingDays;
                }
            }
            return $totalAmount;
        } else {
            foreach ($this->budgetRepo->getAll() as $budget) {
                if ($budget->start()->isAfter($period->end()) || $budget->end()->isBefore($period->start())) {
                    $overlappingDays = 0;
                } else {
                    if ($budget->start()->isBefore($period->start())) {
                        $overlappingStart = $period->start()->day;
                        $overlappingEnd = $budget->end()->day;
                    } else if ($budget->end()->isAfter($period->end())) {
                        $overlappingStart = $budget->start()->day;
                        $overlappingEnd = $period->end()->day;
                    } else if ($budget->start()->isAfter($period->start()) && $budget->end()->isBefore($period->end())) {
                        $overlappingStart = $budget->start()->day;
                        $overlappingEnd = $budget->end()->day;
                    }
                    $overlappingDays = $overlappingEnd - $overlappingStart + 1;
                }
                $totalAmount += $budget->dailyAmount() * $overlappingDays;
            }
        }
        return $totalAmount;
    }

}