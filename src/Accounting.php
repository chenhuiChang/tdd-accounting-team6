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
    private $budgets;


    /**
     * Accounting constructor.
     * @param IBudgetRepo $budgetRepo
     */
    public function __construct(IBudgetRepo $budgetRepo)
    {
        $this->budgets = $budgetRepo->getAll();
    }

    public function totalAmount(Carbon $start, Carbon $end)
    {
        if (empty($this->budgets)) {
            return 0;
        }
        return $start->diffInDays($end) + 1;
    }
}