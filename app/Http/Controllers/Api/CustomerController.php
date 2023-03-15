<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Services\LoanService;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;

use function PHPUnit\Framework\throwException;

class CustomerController extends Controller
{
    const ROLE_CUSTOMER = 'customer';
    const LOAN_STATUS_PENDING = 'PENDING';
    const LOAN_STATUS_APPROVE = 'APPROVE';
    const LOAN_STATUS_PAID = 'PAID';

    public function createLoan(Request $request, LoanService $loanService)
    {
        $role = $request?->user()?->role;
        if ($role !== $this::ROLE_CUSTOMER) {
            return response()->json(['message' => 'Permission denied.'], 401);
        }

        $validateLoan = Validator::make($request->all(), ['term' => 'required', 'amount' => 'required']);
        if ($validateLoan->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'validation error',
                'errors' => $validateLoan->errors()
                ], 401);
        }
        $loan = Loan::create([
            'status' => $this::LOAN_STATUS_PENDING,
            'user_id' => $request->user()->id,
            'amount' => $request->amount,
            'term' => $request->term,
            ]);

        $paymentSchedule = $loanService->scheduleRepayment($loan->created_at, $loan->term, $loan->amount);
        return response()->json([
            'status' => true,
            'message' => 'Create Loan Successfully',
            'loan' => $loan,
            'paymentSchedule' => $paymentSchedule
            ], 200);

    }
}
