<?php

namespace Tests\Unit;

use App\Services\LoanService;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class LoanServiceTest extends TestCase
{
    /**
     * Test schedule repayment.
     *
     * @return void
     */
    public function test_schedule_repayment_can_generate_correctly()
    {
        $loanService = new LoanService();
        $loanDate = new Carbon('2022-02-07');
        $term = 3;
        $amount = 10000;

        $scheduleRepayments = $loanService->scheduleRepayment($loanDate, $term, $amount);
        $expectedScheduleRepayments = [
            [
                'due_date' => '2022-02-14',
                'amount_format' => '3.333,33',
                'amount' => 3333.33
            ],
            [
                'due_date' => '2022-02-21',
                'amount_format' => '3.333,33',
                'amount' => 3333.33
            ],
            [
                'due_date' => '2022-02-28',
                'amount_format' => '3.333,34',
                'amount' => 3333.34
            ]
        ];
        $this->assertEquals(json_encode($scheduleRepayments), json_encode($expectedScheduleRepayments));
    }
}
