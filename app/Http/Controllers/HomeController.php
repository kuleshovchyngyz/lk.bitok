<?php

namespace App\Http\Controllers;

use App\Http\Resources\ImportLogResource;
use App\Models\BlacklistLogs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class HomeController extends Controller
{


    public function exportCollection(Request $request)
    {
//        $collection = // ... get your collection here

//    return Excel::download(new CollectionExport($collection), 'collection.xlsx');
    }

    public function import2()
    {
        $fileUrl = 'https://fiu.gov.kg/site/private';
        $response = Http::post('https://fiu.gov.kg/user/login', [
            'LoginForm[username]' => 'bitokkg',
            'LoginForm[password]' => '!@bitok2020',
        ])->withHeaders([
            'Referer' => 'https://fiu.gov.kg/user/login'
        ]);
        $cookies = $response->cookies();

// Download the XML file using the stored session cookies
        $file = Http::withCookies($cookies)->get($fileUrl);
        return $file;
        Storage::put('file.xml', $file->body());
    }

    public function import1()
    {
        $username = 'maksat607@gmail.com';
        $password = 'password';
        $response = Http::withHeaders([
            'Content-Type' => 'application/x-www-form-urlencoded',
        ])->post('https://myparking.info/login', [
            'LoginForm[username]' => $username,
            'LoginForm[password]' => $password,
            '_token' => 'KiVi3Kr4FYnGkkpbBn0gGhu28NMhCmZ8YFnbaUlg'
        ]);
//        dump($response);
        $fileUrl = 'https://myparking.info/applications/2';

        $cookies = $response->cookies()->toArray();
        $file = Http::withCookies($cookies, true)->get($fileUrl);
        return $file;

    }

    public function import()
    {

//        $loginUrl = 'https://fiu.gov.kg/user/login';
        $loginUrl = 'https://myparking.info/login';
        $username = 'maksat607@gmail.com';
        $password = 'password';
//    $username = 'bitokkg';
//    $password = '!@bitok2020';
        $login = Http::asForm()->post($loginUrl, [
            "email" => $username,
            "password" => $password,
            "_token" => "LgDS8iNhABx55uEvmIqj4muAa3kN26tq8gC6xGNK"
        ]);
        dump($login);
        $sessionCookie = $login
            ->cookies()
            ->getCookieByName("myparkinginfo_session")
            ->toArray();
        dump($sessionCookie);
        $sessionCookieName = $sessionCookie["Name"];
        $sessionCookieValue = $sessionCookie["Value"];

        $fileUrl = 'https://fiu.gov.kg/site/private';
        $fileUrl = 'https://myparking.info/applications/2';
//    $fileContents = Http::withCookies($cookieJar->toArray(),false)->get($fileUrl)->body();
//
//// Save the file to disk


        $fileContents = Http::withHeaders([
            "Cookie" => "myparkinginfo_session" . "=" . $sessionCookieValue,
        ])->get($fileUrl)->body();

        $filename = 'private_file.html';
        file_put_contents($filename, $fileContents);
        return $fileContents;
    }


}
