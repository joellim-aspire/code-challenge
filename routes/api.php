<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\RepaymentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//User Routes
Route::post('/users/create', [UserController::class, 'create_user']);
Route::get('/users', [UserController::class, 'get_all_users']);
Route::get('/user/{user_id}', [UserController::class, 'get_user_by_id']);

//Loan Routes
Route::post('/loans/create', [LoanController::class, 'create_loan']);
Route::get('/loans', [LoanController::class, 'get_all_loans']);
Route::get('/loan/{id}', [LoanController::class, 'get_loan_by_id']);
Route::post('/loan/approve/{id}', [LoanController::class, 'approve_loan_by_id']);
Route::get('/loans/user/{user_id}', [LoanController::class, 'get_loans_by_userid']);

//Repayment Routes
Route::post('/repayments/create', [RepaymentController::class, 'create_repayment']);
Route::get('/repayments', [RepaymentController::class, 'get_all_repayments']);
Route::get('/repayment/{id}', [RepaymentController::class, 'get_repayment_by_id']);
Route::get('/repayments/loan/{loan_id}', [RepaymentController::class, 'get_repayments_by_loanid']);


