<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Loan;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;


class LoanController extends Controller
{
    function create_loan(Request $request) {
        $user = User::find($request->user_id); // check if user_id is valid first
        if ($user) {
            $loan = new Loan();
            $loan->user_id = $user->id;
            $loan->loan_term = $request->loan_term;
            $loan->amount_required = $request->amount_required;
            $loan->save();
            return($loan);
        } else {
            $format = 'There is no user with id %d';
            echo sprintf($format, $request->user_id);
        }
    }

    function get_loan_by_id(int $id) {
        return Loan::find($id);
    }

    function get_all_loans() {
        return Loan::all();
    }

    function approve_loan_by_id(int $id, Request $request) {
        // TO-DO: check if requester isAdmin first
        $loan = $this->get_loan_by_id($id); //get loan
        if ($loan) {
            $loan->status = 'Approved';
            $loan->approved_at = Carbon::now();
            $loan->updated_at = Carbon::now();
            $loan->save();
            echo($loan);
        } else {
            $format = 'There is no loan with id %d';
            echo sprintf($format, $id);
        }
    }
}
