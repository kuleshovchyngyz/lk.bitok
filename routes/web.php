<?php

use Illuminate\Support\Facades\Route;
use Orchestra\Parser\Xml\Facade as XmlParser;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {

    return view('welcome');
});

//Route::get('/pft', [\App\Http\Controllers\HomeController::class,'pft']);
//Route::get('/lpdp', [\App\Http\Controllers\HomeController::class,'lpdp']);
//Route::get('/forall', [\App\Http\Controllers\HomeController::class,'forall']);
