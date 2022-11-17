<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\Api\AuthController;
use \App\Http\Controllers\Api\UserOperationController;
use \App\Http\Controllers\Api\AddedUserController;
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


Route::post('auth/register', [AuthController::class,'register']);
Route::post('auth/login', [AuthController::class,'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('auth/me', [AuthController::class,'me']);
    Route::get('auth/logout', [AuthController::class,'logout']);
    Route::resource('added-users', AddedUserController::class);
    Route::resource('user-operations', UserOperationController::class);

    Route::apiResource('added-users.user-operations', UserOperationController::class)->shallow();

    Route::post('added-users/search', [AddedUserController::class,'search']);
    Route::post('user-operations/search', [UserOperationController::class,'search']);
    Route::get('countries', [AddedUserController::class,'countries']);
});
