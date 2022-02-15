<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\User;
use App\Models\Repayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RepaymentController extends Controller
{
    function create_repayment(Request $request) {
        $user = new User();
        $user->username = $request->username;
        $user->password = Hash::make($request->password);
        $user->isAdmin = $request->isAdmin;
        $user->save();

        $loan = new Loan();
        $loan->user_id = $user->id;
        $loan->loan_term = 3;
        $loan->amount_required = 100000;
        $loan->save();

        $repayment = new Repayment();
        $repayment->loan_id = $loan->id;
        $repayment->amount_paid = 1000;
        $repayment->save();
        return($repayment);
    }

    function get_repayment_by_id(int $id) {
        return Repayment::find($id);
    }

    function get_all_repayments() {
        return Repayment::all();
    }

}
