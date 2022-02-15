<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    function create_user(Request $request) {
        $user = new User();
        $user->username = $request->username;
        $user->password = Hash::make($request->password);
        $user->isAdmin = $request->isAdmin;
        $user->save();
        print($user);
    }

    function get_user_by_id(int $id) {
        return User::find($id);
    }

    function get_all_users() {
        return User::all();
    }

    function get_loans_by_userid(int $id) {
        $user = User::find($id);
        return $user->loans();
    }
}
