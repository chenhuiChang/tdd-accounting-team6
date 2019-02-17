<?php
/**
 * Created by PhpStorm.
 * User: alexchang
 * Date: 2019-02-16
 * Time: 15:20
 */

namespace Tests;

use App\Accounting;
use App\Budget;
use App\IBudgetRepo;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class AccountingTest extends TestCase
{

    /**
     * @var Accounting
     */
    private $accounting;
    /**
     * @var FakeBudgetRepo
     */
    private $fakeBudgetRepo;

    public function test_no_budgets()
    {
        $this->accounting = new Accounting($this->fakeBudgetRepo);
        $this->amountShouldBe(0,
            Carbon::create(2019, 4, 1),
            Carbon::create(2019, 4, 1));
    }

    public function test_periods_inside_budget_month()
    {
        $this->fakeBudgetRepo->setBudgets([
            new Budget('201904', 30)
        ]);
        $this->accounting = new Accounting($this->fakeBudgetRepo);
        $this->amountShouldBe(1,
            Carbon::create(2019, 4, 1),
            Carbon::create(2019, 4, 1));
    }

    protected function setUp()
    {
        parent::setUp();
        $this->fakeBudgetRepo = new FakeBudgetRepo();
    }

    /**
     * @param $start
     * @param $end
     * @param $expected
     */
    private function amountShouldBe($expected, $start, $end): void
    {
        $this->assertEquals($expected, $this->accounting->totalAmount($start, $end));
    }


}
