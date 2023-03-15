<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\SchedulePayment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    const ROLE_ADMIN = 'admin';

    public function approveLoan(Request $request)
    {
        $role = $request?->user()?->role;
        if ($role !== $this::ROLE_ADMIN) {
            return response()->json(['message' => 'Permission denied.'], 401);
        }
        $validateLoan = Validator::make($request->all(), ['loanId' => 'required|numeric']);
        if ($validateLoan->fails()) {
            return response()->json(
                [
                'status' => false,
                'message' => 'validation error',
                'errors' => $validateLoan->errors()
                ], 401
            );
        }
        $loan = Loan::findOrFail($request->loanId);
        $loan->update(['status' => Loan::STATUS_APPROVE]);
        SchedulePayment::where(['loan_id' => $loan->id])->update(['status' => Loan::STATUS_APPROVE]);
        return response()->json(
            [
            'status' => true,
            'message' => 'Approve Loan Successfully',
            'loan' => $loan,
            ], 200
        );
    }
}
