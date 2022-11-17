<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\Api\AuthController;
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
    Route::resource('added-users', \App\Http\Controllers\Api\AddedUserController::class);
    Route::resource('user-operations', \App\Http\Controllers\Api\UserOperationController::class);

    Route::post('added-users/search', [\App\Http\Controllers\Api\AddedUserController::class,'search']);
    Route::get('countries', [\App\Http\Controllers\Api\AddedUserController::class,'countries']);
});
