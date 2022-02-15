<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\User;
use App\Models\Repayment;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class RepaymentController extends Controller
{
    function create_repayment(Request $request) {
        $loan = LoanController::get_loan_by_id($request->loan_id); // check if loan_id is valid first
        if ($loan) {
            if ($loan->status == 'Pending') {
                echo ("Loan is still pending. ");
            } elseif ($loan->status == 'Paid') {
                echo ("Loan has already been paid. ");
            } else {
                if ($request->amount > $loan->amount_balance) {
                    echo ("Repayment is more than Loan Amount. Please input a lower Repayment Amount. ");
                    exit();
                }
                $repayments = Repayment::pending()->belongs_to($request->loan_id)->get(); //get all pending repayments
                $repayment_amended = $repayments->first();
                $amount_paid = $request->amount;
                if ($amount_paid < $repayment_amended->amount) {
                    echo("Repayment is less than Repayment Amount. Please input a higher Repayment Amount. ");
                    exit();
                }
                $repayment_amended->status = 'Paid';
                $repayment_amended->save();

                $loan->loan_term_remaining--;
                $loan->amount_balance = $loan->amount_balance - $amount_paid;
                if ($loan->loan_term_remaining > 0) {
                    $scheduled_repayment = round(($loan->amount_balance / $loan->loan_term_remaining));
                    $repayments->skip(1)->each(function ($repayment, $key, $scheduled_repayment) { //SOLVE SCHEDULED REPAYMENT PROBLEM, NEED SKIP THE FIRST ONE
                        $repayment->amount = $scheduled_repayment;
                        $repayment->save();
                    });
                } else {
                    $loan->status = 'Paid';
                }
                $loan->save();
                return ($repayment_amended);
            }
        }
    }

    static function get_repayment_by_id(int $loan_id) {
        return Repayment::find($loan_id);
    }

    static function get_all_repayments() {
        return Repayment::all();
    }

    function get_repayments_by_loanid(int $loan_id) {
        $loan = LoanController::get_loan_by_id($loan_id);
        if ($loan) {
            $repayments = Repayment::belongs_to($loan_id)->orderBy('id')->get();
            return $repayments;
        }
    }
}
