<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\User;
use App\Models\Repayment;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class RepaymentController extends Controller
{
    function createRepayment(Request $request, int $loan_id) {
        $loan = Loan::find($loan_id); // check if loan_id is valid first

        if (!$loan) {
            return response([
                'message' => 'Loan does not exist.'
            ], 200);
        }

        if ($loan->status == 'Pending') {
            return response([
                'message' => 'Loan is still pending.'
            ], 200);
        }

        if ($loan->status == 'Paid') {
            return response([
                'message' => 'Loan has been paid.'
            ], 200);
        }

        $repayments = Repayment::pending()->belongs_to($loan_id)->get(); //get all pending repayments
        $repayment_amended = $repayments->first(); //get first pending repayment
        $amount_paid = $request->amount;

        if ($amount_paid > $loan->amount_balance) {
            return response([
                'message' => 'Repayment is more than Loan Balance. Please input a lower Repayment Amount.'
            ], 200);
        }

        if ($amount_paid < $repayment_amended->amount) {
            return response([
                'message' => 'Repayment is less than payable Amount. Please input a higher Repayment Amount.'
            ], 200);
        }

        $repayment_amended->status = 'Paid';
        $repayment_amended->amount = $amount_paid;
        $repayment_amended->save();

        $loan->loan_term_remaining--;
        $loan->amount_balance = $loan->amount_balance - $amount_paid;
        $loan->save();
        echo("Here1");
        if ($loan->loan_term_remaining == 0) {
            echo("Here2");
            $loan->status = 'Paid';
            echo("Here3");
            $loan->save();
            echo("Here4");
            return response([
                'message' => 'All repayments have been paid. Loan has been paid.'
            ], 200);
        }
        echo("Here5");
        $scheduled_repayment = floor(($loan->amount_balance * 100/ $loan->loan_term_remaining)) / 100;
        $repayments->skip(1)->each(function ($repayment) use($scheduled_repayment) {
            $repayment->amount = $scheduled_repayment;
            $repayment->save();
        });
        echo("Here6");

        return response([
            'repayment_amended' => $repayment_amended,
            'message' => 'The scheduled repayment has been paid.'
        ], 200);
    }

    static function viewOwnRepaymentsByLoanId(Request $request, int $loan_id): Response {
        $user = $request->user();
        $loans = Loan::belongs_to($user->id)->orderByDesc('id')->get();
        $loan = LoanController::get_loan_by_id($loan_id);

        if (!$loans->contains($loan)) {
            return response([
                'message' => 'You do not have a loan containing that Loan_ID.'
            ], 200);
        }

        return response([
            'loan' => $loan
        ], 200);
    }

    static function getAllRepayments(): Collection {
        return Repayment::all();
    }

//    function get_repayments_by_loanid(int $loan_id) {
//        $loan = LoanController::get_loan_by_id($loan_id);
//        if ($loan) {
//            $repayments = Repayment::belongs_to($loan_id)->orderBy('id')->get();
//            return $repayments;
//        }
//    }
}
