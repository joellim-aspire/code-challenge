<?php

namespace App\Http\Controllers;

use App\Models\Repayment;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Loan;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;


class LoanController extends Controller
{
    function createLoan(Request $request): Response {
        $user = $request->user();

        $loan = new Loan();
        $loan->user_id = $user->id;
        $loan->loan_term = $request->loan_term;
        $loan->loan_term_remaining = $request->loan_term;
        $loan->amount_required = $request->amount_required;
        $loan->amount_balance = $request->amount_required;
        $loan->loan_start_date = $request->loan_start_date; // how to change to allow user to input loan date?
        if ($loan->loan_start_date < Carbon::now()) {
            return response([
                'message' => 'Loan Start Date is before current Date. Please re-select. '
            ], 200);
        }
        $loan->save();

        // generate scheduled repayments based on loan term and amount when loan is being created
        $scheduled_repayment = floor(($request->amount_required * 100/ $request->loan_term)) / 100;
        $start_date = $loan->loan_start_date;
        $repayment_date = date('Y-m-d', strtotime($start_date. ' + 7 days'));
        for ($count = 0; $count < ($loan->loan_term)-1; $count++) {
            $repayment = new Repayment();
            $repayment->repayment_date = $repayment_date;
            $repayment_date = date('Y-m-d', strtotime($repayment_date. ' + 7 days'));
            $repayment->loan_id = $loan->id;
            $repayment->amount = $scheduled_repayment;
            $repayment->save();
        }
        $repayment = new Repayment();
        $repayment->repayment_date = $repayment_date;
        $repayment->loan_id = $loan->id;
        $repayment->amount = $loan->amount_required - $scheduled_repayment * (($loan->loan_term)-1);
        $repayment->save();
        return response([
            'message' => 'Loan and scheduled repayments have been created.',
            'loan' => $loan
        ], 200);
    }

    static function getLoanById(int $loan_id): Response {
        $loan = Loan::find($loan_id);
        if ($loan) {
            return response([
                'message' => 'Loan has been found.',
                'loan' => $loan
            ], 200);
        }

        return response([
            'message' => 'Loan does not exist.'
        ], 200);

    }

    static function getAllLoans(): Collection {
        return Loan::all();
    }

    function viewOwnLoans(Request $request): Collection {
        $user = $request->user();
        $loans = Loan::belongs_to($user->id)->orderByDesc('id')->get();
        return $loans;
    }

    function approveLoanById(int $loan_id, Request $request): Response {
        $user = $request->user();

        if($user->isAdmin == 1) {
            $loan = $this->getLoanById($loan_id); //get loan
            if ($loan and $loan->status == 'Pending') {
                $loan->status = 'Approved';
                $loan->approved_at = Carbon::now();
                $loan->save();
                return response([
                    'message' => 'Loan has been approved.',
                    'loan' => $loan
                ], 200);
            }

            return response([
                'message' => 'Loan does not exist.'
            ], 200);
        }

        return response([
            'message' => 'You are not able to approve loans.'
        ], 200);
    }
}
