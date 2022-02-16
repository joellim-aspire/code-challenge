<?php

namespace App\Http\Controllers;

use App\Models\Repayment;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Loan;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;


class LoanController extends Controller
{
    function create_loan(Request $request) {
        $user = UserController::get_user_by_id($request->user_id); // check if user_id is valid first
        if ($user) {
            $loan = new Loan();
            $loan->user_id = $user->id;
            $loan->loan_term = $request->loan_term;
            $loan->loan_term_remaining = $request->loan_term;
            $loan->amount_required = $request->amount_required;
            $loan->amount_balance = $request->amount_required;
            $loan->loan_start_date = $request->loan_start_date; // how to change to allow user to input loan date?
            if ($loan->loan_start_date < Carbon::now()) {
                echo ("Loan Start Date is before current Date. Please re-select. ");
                exit();
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
            echo($loan);
        }
    }

    static function get_loan_by_id(int $loan_id) {
        $loan = Loan::find($loan_id);
        if ($loan) {
            return $loan;
        } else {
            $format = 'There is no User with user_id %d';
            echo sprintf($format, $loan_id);
            exit();
        }
    }

    static function get_all_loans() {
        return Loan::all();
    }

    function get_loans_by_userid(int $user_id) {
        $user = UserController::get_user_by_id($user_id);
        if ($user) {
            $loans = Loan::belongs_to($user_id)->orderByDesc('updated_at')->get();
            return $loans;
        }
    }

    function approve_loan_by_id(int $id, Request $request) {
        // TO-DO: check if requester isAdmin first
        $user = UserController::get_user_by_id($request->approver_id);
        if($user and $user->isAdmin == 1) {
            $loan = $this->get_loan_by_id($id); //get loan
            if ($loan and $loan->status == 'Pending') {
                $loan->status = 'Approved';
                $loan->approved_at = Carbon::now();
                $loan->save();
                echo($loan);
            } else {
                $format = 'There is no pending Loan with ID %d to approve.';
                echo sprintf($format, $id);
            }
        } else {
            echo("You are not authorized to approve this loan. ");
        }

    }
}
