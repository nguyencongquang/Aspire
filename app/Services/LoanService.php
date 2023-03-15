<?php

namespace App\Services;

class LoanService
{
    public function scheduleRepayment($loanDate, $term, $amount) {
        $repayments = [];
        for ($i=1; $i<=$term; $i++) {

            $nextPaymentDate = $loanDate->addWeeks(1)->format('Y-m-d');
            $repayment = [
                'date' => $nextPaymentDate
            ];
            $repayments[] = $repayment;
        }
        return $repayments;
    }
}