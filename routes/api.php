<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\Api\AuthController;
use \App\Http\Controllers\Api\UserOperationController;
use \App\Http\Controllers\Api\AddedUserController;
use \App\Http\Controllers\Api\SanctionController;
use \App\Http\Controllers\Api\CountryController;
use \App\Http\Controllers\V1\CarController;
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


Route::post('upload',[CarController::class,'upload']);

Route::group(['prefix'=>'v1'],function () {

    Route::get('cars',[CarController::class,'carId']);

    Route::get('types', [CarController::class, 'carTypes']);
    Route::get('types/{carType}',[CarController::class,'carType']);


    Route::get('types/{carType}/marks', [CarController::class, 'carMarks']);
    Route::get('marks/{carMark}',[CarController::class,'carMark']);
    Route::get('marks/{carMark}/models', [CarController::class, 'carModels']);
    Route::get('models/{carModel}',[CarController::class,'carModel']);
    Route::get('models/{carModel}/years', [CarController::class, 'years']);
    Route::get('models/{carModel}/series', [CarController::class, 'getSeriesModel']);
    Route::get('models/{carModel}/years/{year}/generations', [CarController::class, 'generations']);
    Route::get('years/{year}/generations/{carGeneration}',[CarController::class,'generation']);
    Route::get('generations/{carGeneration}/series', [CarController::class, 'series']);

    Route::get('series/{carSeries}',[CarController::class,'serie']);
    Route::get('model/{carModel}/series/{carSeries}/{year}', [CarController::class, 'modifications']);
    Route::get('modifications/{carModification}',[CarController::class,'modification']);
    Route::get('modifications/{carModification}/engines', [CarController::class, 'engines']);
    Route::get('modifications/{carModification}/transmissions', [CarController::class, 'transmissions']);
    Route::get('modifications/{carModification}/gears', [CarController::class, 'gears']);



    Route::get('marklist/{type}', [CarController::class, 'getCarMarkList']);
    Route::post('app', [CarController::class, 'applicationUpdateData']);
    Route::post('title', [CarController::class, 'carTitleData']);
    Route::post('get-cars', [CarController::class, 'getCars']);





});
Route::get('/import', [\App\Http\Controllers\Api\ImportController::class,'import']);
Route::post('/import', [\App\Http\Controllers\Api\ImportController::class,'upload']);
Route::get('/pft', [\App\Http\Controllers\Api\ImportController::class,'pft']);
Route::get('/plpd', [\App\Http\Controllers\Api\ImportController::class,'plpd']);
Route::get('/forall', [\App\Http\Controllers\Api\ImportController::class,'forall']);
Route::get('/un', [\App\Http\Controllers\Api\ImportController::class,'un']);



Route::middleware('auth:sanctum')->group(function () {
    Route::get('auth/me', [AuthController::class,'me']);
    Route::get('auth/logout', [AuthController::class,'logout']);
    Route::resource('added-users', AddedUserController::class);
    Route::resource('legal-entities', \App\Http\Controllers\Api\LegalEntityController::class);
    Route::resource('user-operations', UserOperationController::class);
    Route::resource('settings', \App\Http\Controllers\Api\SettingController::class);
    Route::post('user-operations/range', [UserOperationController::class,'range']);

    Route::apiResource('countries.added-users', AddedUserController::class)->shallow();
    Route::apiResource('added-users.user-operations', UserOperationController::class)->shallow();


    Route::get('legal-entities/{legal-entity}/user-operations', [UserOperationController::class,'legalUserOperations']);
    Route::post('added-users/search', [AddedUserController::class,'search']);
    Route::post('legal-entities/search', [\App\Http\Controllers\Api\LegalEntityController::class,'search']);
    Route::post('added-users/{added_user}/upload', [AddedUserController::class,'upload']);
    Route::delete('attachment/{attachment}/delete', [AddedUserController::class,'delete']);
    Route::post('user-operations/search', [UserOperationController::class,'search']);
//    Route::get('countries', [AddedUserController::class,'countries']);
//    Route::post('countries', [SanctionController::class,'store']);
//    Route::put('countries', [SanctionController::class,'update']);
    Route::resource('countries',CountryController::class);
    Route::post('countries/bulk',[CountryController::class,'bulkUpdate']);
});
