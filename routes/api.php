<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AdminController;

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

Route::middleware('auth:sanctum')->get(
    '/user', function (Request $request) {
        return $request->user();
    }
);

Route::post('/auth/login', [AuthController::class, 'loginUser']);

Route::post('/customer/createLoan', [CustomerController::class, 'createLoan'])->middleware('auth:sanctum');
Route::get('/customer/loan/{id}', [CustomerController::class, 'viewLoan'])->middleware('auth:sanctum');
Route::post('/customer/payment/{id}/pay', [CustomerController::class, 'addRepayment'])->middleware('auth:sanctum');

Route::post('/admin/approveLoan', [AdminController::class, 'approveLoan'])->middleware('auth:sanctum');

