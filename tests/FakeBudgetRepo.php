<?php
/**
 * Created by PhpStorm.
 * User: alexchang
 * Date: 2019-02-17
 * Time: 17:04
 */

namespace Tests;


use App\IBudgetRepo;

class FakeBudgetRepo implements IBudgetRepo
{
    private $budgets;

    /**
     * FakeBudgetRepo constructor.
     */
    public function __construct()
    {
    }

    public function getAll()
    {
        return $this->budgets;
    }

    public function setBudgets(array $budgets)
    {
        $this->budgets = $budgets;
    }

}