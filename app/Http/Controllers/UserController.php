<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Response;

class UserController extends Controller
{
    static function getUserById(int $user_id): Response {
        $user = User::find($user_id);
        if ($user) {
            return response([
                'message' => 'User has been found.',
                'user' => $user
            ], 200);
        } else {
            return response([
                'message' => 'User does not exist.'
            ], 200);
        }
    }

    static function getAllUsers(): Collection {
        return User::all();
    }
}
