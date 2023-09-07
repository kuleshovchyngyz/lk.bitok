<?php

namespace App\Http\Controllers;

use App\Http\Resources\ImportLogResource;
use App\Models\BlacklistLogs;
use Database\Seeders\CarDatabaseSeeder;
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

    }

    public function cars(){
        dd(1212);
        $carSeeder = new CarDatabaseSeeder();
        $carSeeder->run();
    }

}
