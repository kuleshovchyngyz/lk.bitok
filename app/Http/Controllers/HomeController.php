<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Carbon\Carbon;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;

class HomeController extends Controller
{
    use App\Exports\CollectionExport;


    public function exportCollection(Request $request)
    {
//        $collection = // ... get your collection here

//    return Excel::download(new CollectionExport($collection), 'collection.xlsx');
}
    public function import2(){
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
    public function import1(){
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
            $file = Http::withCookies($cookies,true)->get($fileUrl);
          return $file;

    }
    public function import(){

//        $loginUrl = 'https://fiu.gov.kg/user/login';
        $loginUrl = 'https://myparking.info/login';
    $username = 'maksat607@gmail.com';
    $password = 'password';
//    $username = 'bitokkg';
//    $password = '!@bitok2020';
        $login = Http::asForm()->post($loginUrl, [
            "email" => $username,
            "password" => $password,
            "_token"=>"LgDS8iNhABx55uEvmIqj4muAa3kN26tq8gC6xGNK"
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
    public function pft(){
        $file = public_path('blacklist').'/63c7b7007b0e1.xml';

        $xmlString = file_get_contents($file);
        $xmlObject = simplexml_load_string($xmlString);
//        dump($xmlObject);

        $json = json_encode($xmlObject);

        $phpArray = json_decode($json, true);


        $data = [];
        foreach ($phpArray['PersonServedSentence'] as $key=>$item){
            $data[$key]['first_name'] = $item['Name'];
            $data[$key]['last_name'] = $item['Surname'];
            $data[$key]['middle_name'] = $item['Patronomic'];
            $data[$key]['PlaceBirth'] = $item['PlaceBirth'];
            $data[$key]['birth_date'] = \Carbon\Carbon::parse($item['DataBirth']);
            $data[$key]['BasicInclusion'] = $item['BasicInclusion'];
            $data[$key]['type'] = 'pft';
            $data[$key]['country_id'] = 1;

        }
//    dd($data);

        \App\Models\BlackList::upsert($data,['id'], ['first_name', 'last_name','middle_name','birth_date','type']);
//    dd($data);

    }
    public function lpdp(){
        $file = public_path('blacklist').'/63ce72ce20dab.xml';

        $xmlString = file_get_contents($file);

        $xmlObject = simplexml_load_string($xmlString);
//        dd($xmlObject);

        $json = json_encode($xmlObject);

        $phpArray = json_decode($json, true);

        $data = [];
        foreach ($phpArray['LegalizationPhysic'] as $key=>$item){
            $data[$key]['first_name'] = $item['Name'];
            $data[$key]['last_name'] = $item['Surname'];
            $data[$key]['middle_name'] = $item['Patronomic'];
            $data[$key]['birth_date'] = \Carbon\Carbon::parse($item['DataBirth']);
            $data[$key]['BasicInclusion'] = $item['BasicInclusion'];
            $data[$key]['type'] = 'plpd';
            $data[$key]['country_id'] = 1;

        }
//    dd($data);

        \App\Models\BlackList::upsert($data,['id'], ['first_name', 'last_name','middle_name','birth_date','type']);
//    dd($data);

    }
    public function forall(){
        $file = public_path('blacklist').'/63c915bc95a68.xml';

        $xmlString = file_get_contents($file);

        $xmlObject = simplexml_load_string($xmlString);
//        dd($xmlObject);

        $json = json_encode($xmlObject);

        $phpArray = json_decode($json, true);

//        dd($phpArray);
        $data = [];
        foreach ($phpArray['physicPersons']['KyrgyzPhysicPerson'] as $key=>$item){
            $data[$key]['first_name'] = $item['Name'] ?: '';
            $data[$key]['last_name'] = $item['Surname'] ?: '';
            $data[$key]['middle_name'] = $item['Patronomic'] ?: '';
            $data[$key]['PlaceBirth'] = $item['PlaceBirth'] ?: '';//"DataBirth" => "24.04.1975"
            $data[$key]['birth_date'] = \Carbon\Carbon::parse(explode(',',$item['DataBirth'])[0])->format('Y-m-d');
            $data[$key]['BasicInclusion'] = $item['BasicInclusion'] ?: '';
            $data[$key]['type'] = 'forall';
            $data[$key]['country_id'] = 1;
//            \App\Models\BlackList::insert($data[$key]);
        }
//dump($data[443]);
//        dump($data[444]);
//        dump($data[445]);
//        dd($data[446]);

        \App\Models\BlackList::upsert($data,['id'], ['first_name', 'last_name','middle_name','birth_date','type']);
//    dd($data);

    }
}
