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

    static function get_user_by_id(int $user_id) {
        $user = User::find($user_id);
        if ($user) {
            return $user;
        } else {
            $format = 'There is no User with user_id %d. ';
            echo sprintf($format, $user_id);
            exit();
        }
    }

    static function get_all_users() {
        return User::all();
    }
}
