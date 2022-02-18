<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\RepaymentController;
use App\Http\Controllers\AuthController;

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

// User Routes
// Protected
Route::group(['middleware' => ['auth:sanctum', 'abilities:loan-approve']], function() { //admin only
    Route::get('/users', [UserController::class, 'getAllUsers']);
    Route::get('/users/{user_id}', [UserController::class, 'getUserById']);

});


// Loan Routes
// Protected
Route::group(['middleware' => ['auth:sanctum', 'abilities:loan-approve']], function() { //admin only
    Route::get('/loans', [LoanController::class, 'getAllLoans']);
    Route::post('/loan/approve/{loan_id}', [LoanController::class, 'approveLoanById']);
});

Route::group(['middleware' => ['auth:sanctum', 'abilities:loan-create']], function() {
    Route::get('/loans/user', [LoanController::class, 'viewOwnLoans']);
    Route::post('/loans/create', [LoanController::class, 'createLoan']);
});
// Public


// Repayment Routes
// Protected
Route::group(['middleware' => ['auth:sanctum', 'abilities:loan-approve']], function() { //admin only
    Route::get('/repayments', [RepaymentController::class, 'getAllRepayments']);
});

Route::group(['middleware' => ['auth:sanctum', 'abilities:loan-create']], function() {
    Route::post('/repayments/create/{loan_id}', [RepaymentController::class, 'createRepayment']);
    Route::get('/repayments/loan/{loan_id}', [RepaymentController::class, 'viewOwnRepaymentsByLoanId']);
});
// Public


// Auth Routes
// Protected
Route::group(['middleware' => ['auth:sanctum']], function() {
    Route::post('/logout', [AuthController::class, 'logout']);
});
// Public
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);



