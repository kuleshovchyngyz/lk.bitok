<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;

class HomeController extends Controller
{
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
