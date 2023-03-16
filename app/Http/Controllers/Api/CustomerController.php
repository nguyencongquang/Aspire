<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\SchedulePayment;
use App\Services\LoanService;
use Carbon\Carbon;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;

use function PHPUnit\Framework\throwException;

class CustomerController extends Controller
{
    const ROLE_CUSTOMER = 'customer';

    /**
     * Customer can create a loan
     * @param  Request  $request
     * @param  LoanService  $loanService
     * @return \Illuminate\Http\JsonResponse
     */
    public function createLoan(Request $request, LoanService $loanService)
    {
        $role = $request?->user()?->role;
        if ($role !== $this::ROLE_CUSTOMER) {
            return response()->json(['message' => 'Permission denied.'], 401);
        }

        $validateLoan = Validator::make($request->all(), ['term' => 'required|numeric', 'amount' => 'required|numeric']);
        if ($validateLoan->fails()) {
            return response()->json(
                [
                'status' => false,
                'message' => 'validation error',
                'errors' => $validateLoan->errors()
                ], 401
            );
        }
        $loan = Loan::create(
            [
            'status' => Loan::STATUS_PENDING,
            'user_id' => $request->user()->id,
            'amount' => $request->amount,
            'term' => $request->term,
            ]
        );

        $schedulePayments = $loanService->scheduleRepayment($loan->created_at, $loan->term, $loan->amount);
        $tempSchedulePayments = $schedulePayments;
        foreach ($tempSchedulePayments as &$tempSchedulePayment) {
            $tempSchedulePayment['amount'] = floatval($tempSchedulePayment['amount']);
            $tempSchedulePayment['loan_id'] = $loan->id;
            $tempSchedulePayment['created_at'] = Carbon::now();
            $tempSchedulePayment['updated_at'] = Carbon::now();
            unset($tempSchedulePayment['amount_format']);
        }
        SchedulePayment::insert($tempSchedulePayments);

        return response()->json(
            [
            'status' => true,
            'message' => 'Create Loan Successfully',
            'loan' => $loan,
            'schedulePayments' => $schedulePayments
            ], 200
        );

    }

    /**
     * Customer can view his loan
     * @param $id
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function viewLoan($id, Request $request)
    {
        $loan = Loan::findOrFail($id);
        if ($loan->user_id !== $request?->user()?->id) {
            return response()->json(['message' => 'You do not have permission to view this loan.'], 401);
        }

        return response()->json(
            [
                'status' => true,
                'message' => 'Get Loan Successfully',
                'loan' => $loan,
                'schedulePayments' => $loan->schedulePayments
            ], 200
        );
    }

    /**
     * Customer add a repayments
     * @param $id
     * @param  Request  $request
     * @param  LoanService  $loanService
     * @return \Illuminate\Http\JsonResponse
     */
    public function addRepayment($id, Request $request, LoanService $loanService)
    {
        $validateLoan = Validator::make($request->all(), ['amount' => 'required|numeric']);
        if ($validateLoan->fails()) {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateLoan->errors()
                ], 401
            );
        }

        $scheduleRepayment = SchedulePayment::findOrFail($id);
        if ($scheduleRepayment->loan->user_id !== $request?->user()?->id) {
            return response()->json(['message' => 'Invalid payment.'], 401);
        }

        if ($request->amount < $scheduleRepayment->amount) {
            return response()->json(['message' => 'You should add a repayment with amount greater or equal to the scheduled repayment'.'('.$scheduleRepayment->amount.')'], 401);
        }
        $scheduleRepayment->update(['customer_input_amount' => $request->amount, 'status' => Loan::STATUS_PAID]);
        $loanService->autoUpdateLoanStatusToPaid($scheduleRepayment->loan->id);

        return response()->json(
            [
                'status' => true,
                'message' => 'Add Repayment Successfully.',
            ], 200
        );

    }
}
