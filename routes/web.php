<?php

use Illuminate\Support\Facades\Route;
use Orchestra\Parser\Xml\Facade as XmlParser;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Cookie\CookieJar;
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


// Set your login credentials
    $username = 'bitokkg';
    $password = '!@bitok2020';

// Log in and get cookies
    $loginUrl = 'https://fiu.gov.kg/user/login';
    $cookieJar = new CookieJar();
    Http::withHeaders([
        'Referer' => $loginUrl,
    ])->withOptions([
        'cookies' => $cookieJar,
    ])->post($loginUrl, [
        'login-form[login]' => $username,
        'login-form[password]' => $password,
        '_token' => `p97gRLsyUDFaAyN1dUOdeO5xbfWILrU_TZk5jFf-1mjtuJp3011kay5JVSImdeoeq0Rdkcto73ou_2_nJomHOg==`,
    ]);

// Download the file using the cookies
    $fileUrl = 'https://fiu.gov.kg/site/private';
    $fileContents = Http::withCookies($cookieJar->toArray(),false)->get($fileUrl)->body();

// Save the file to disk
    $filename = 'private_file.html';
    file_put_contents($filename, $fileContents);

    return view('welcome');
});

//Route::get('/pft', [\App\Http\Controllers\HomeController::class,'pft']);
//Route::get('/lpdp', [\App\Http\Controllers\HomeController::class,'lpdp']);
//Route::get('/forall', [\App\Http\Controllers\HomeController::class,'forall']);
