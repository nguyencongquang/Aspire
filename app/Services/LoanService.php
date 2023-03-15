<?php

namespace App\Services;

use App\Models\Loan;
use App\Models\SchedulePayment;

class LoanService
{
    public function scheduleRepayment($loanDate, $term, $amount)
    {
        $repayments = [];
        $sumUpPay = 0;
        for ($i=1; $i<=$term; $i++) {

            $nextPaymentDate = $loanDate->addWeeks(1)->format('Y-m-d');
            $pay = round($amount/$term, 2);
            if ($i < $term) {
                $sumUpPay+= $pay;
            }
            if ($i == $term) {
                $pay = round($amount-$sumUpPay, 2);;
            }
            $repayment = [
                'due_date' => $nextPaymentDate,
                'amount_format' => number_format($pay, 2, ',', '.'),
                'amount' => $pay
            ];
            $repayments[] = $repayment;
        }

        return $repayments;
    }

    public function autoUpdateLoanStatusToPaid($loanId)
    {
        $schedulePayments = SchedulePayment::where('loan_id', $loanId)->get();
        foreach ($schedulePayments as $schedulePayment) {
            if ($schedulePayment->status !== Loan::STATUS_PAID) {
                return false;
            }
        }
        return Loan::find($loanId)->update(['status' => Loan::STATUS_PAID]);
    }
}
