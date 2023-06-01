<?php

use App\Models\BlackList;
use Illuminate\Support\Facades\Route;
use Orchestra\Parser\Xml\Facade as XmlParser;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Client;

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

Route::get('/import2', [\App\Http\Controllers\HomeController::class,'import2']);
Route::get('/import1', [\App\Http\Controllers\HomeController::class,'import1']);
Route::get('/import', [\App\Http\Controllers\HomeController::class,'import']);
Route::get('/', function () {
//dd(1);
//    $response = Http::withHeaders([
//        'Authorization' => 'Bearer ' . `p97gRLsyUDFaAyN1dUOdeO5xbfWILrU_TZk5jFf-1mjtuJp3011kay5JVSImdeoeq0Rdkcto73ou_2_nJomHOg==`,
//        'Accept' => 'application/json',
//    ])->post('http://127.0.0.1:8001/api/added-users/search', [
//        'name' => 'ss',
//
//
//    ]);





//        $addedUsers = BlackList::all();
//        foreach ($addedUsers as $addedUser){
//            $addedUser->hash = md5(trim($addedUser['last_name'] ?? null) . trim($addedUser['first_name'] ?? null) . trim($addedUser['middle_name'] ?? null) .  trim($addedUser->birth_date->format('d/m/Y') ?? null));
//            $addedUser->save();
//        }

//$countries =

//// Set your login credentials
//    $username = 'bitokkg';
//    $password = '!@bitok2020';
//
//// Log in and get cookies
//    $loginUrl = 'https://fiu.gov.kg/user/login';
//    $cookieJar = new CookieJar();
//    Http::withHeaders([
//        'Referer' => $loginUrl,
//    ])->withOptions([
//        'cookies' => $cookieJar,
//    ])->post($loginUrl, [
//        'login-form[login]' => $username,
//        'login-form[password]' => $password,
//        '_token' => `p97gRLsyUDFaAyN1dUOdeO5xbfWILrU_TZk5jFf-1mjtuJp3011kay5JVSImdeoeq0Rdkcto73ou_2_nJomHOg==`,
//    ]);
//
//// Download the file using the cookies
//    $fileUrl = 'https://fiu.gov.kg/site/private';
//    $fileContents = Http::withCookies($cookieJar->toArray(),false)->get($fileUrl)->body();
//
//// Save the file to disk
//    $filename = 'private_file.html';
//    file_put_contents($filename, $fileContents);

    phpinfo();
    return view('welcome');
});

Route::get('/pft', [\App\Http\Controllers\Api\ImportController::class,'pft']);
Route::get('/lpdp', [\App\Http\Controllers\Api\ImportController::class,'lpdp']);
Route::get('/forall', [\App\Http\Controllers\Api\ImportController::class,'forall']);
Route::get('/un', [\App\Http\Controllers\Api\ImportController::class,'un']);
