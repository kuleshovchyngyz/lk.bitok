<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ImportLogResource;
use App\Models\BlacklistLogs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ImportController extends Controller
{
    public function upload(Request $request)
    {
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = $file->getClientOriginalName();
            $file->move(public_path('blacklist'), $filename);
            $url = public_path('blacklist').'/'.$filename;
            $type = $request->get('type');
            return call_user_func_array([$this, $type], [$url]);
        }
    }

    public function import()
    {
        return [
            'pft' => 'Перечень физических лиц...(ПФТ)',
            'plpd' => 'Перечень лиц, групп, организаций...(ПЛПД)',
            'forall' => 'Сводный санкционный перечень Кыргызской Республики',
            'un' => 'Сводный санкционный перечень Совета Безопасности ООН',
        ];
    }

    public function pft($file)
    {
//        $file = public_path('blacklist') . '/63c7b7007b0e1.xml';

        $xmlString = file_get_contents($file);
        $xmlObject = simplexml_load_string($xmlString);

        $json = json_encode($xmlObject);

        $phpArray = json_decode($json, true);
        try {
            $bl = null;
            DB::transaction(function () use ($phpArray, &$bl, $file) {
                $bl = BlacklistLogs::create([
                    'file_name' => basename($file),
                    'bl_name_code' => 'pft',
                    'bl_name' => 'Перечень физических лиц...(ПФТ)',
                    'status' => 'Ошибка в обработке',
                ]);

                $data = [];
                foreach ($phpArray['PersonServedSentence'] as $key => $item) {
                    $data[$key]['first_name'] = $item['Name'];
                    $data[$key]['last_name'] = $item['Surname'];
                    $data[$key]['middle_name'] = $item['Patronomic'];
                    $data[$key]['PlaceBirth'] = $item['PlaceBirth'];
                    $data[$key]['birth_date'] = \Carbon\Carbon::parse($item['DataBirth']);
                    $data[$key]['BasicInclusion'] = $item['BasicInclusion'];
                    $data[$key]['type'] = 'pft';
                    $data[$key]['country_id'] = 1;
                    $data[$key]['created_at'] = now();
                    $data[$key]['updated_at'] = now();
                    $data[$key]['blacklist_log_id'] = $bl->id;
                }
                $size = count($data);
                $inserted = \App\Models\BlackList::insert($data);

                $bl->status = "Успешно обработан {$size} записей";
                $bl->save();
                \App\Models\BlackList::where('type', 'pft')->where('blacklist_log_id', '!=', $bl->id)->delete();
            });
            return new ImportLogResource($bl);
        } catch (\Exception $e) {
            return 'Error: ' . $e->getMessage();
        }

    }

    public function plpd($file)
    {
//        $file = public_path('blacklist') . '/63ce72ce20dab.xml';

        $xmlString = file_get_contents($file);

        $xmlObject = simplexml_load_string($xmlString);
//        dd($xmlObject);

        $json = json_encode($xmlObject);

        $phpArray = json_decode($json, true);
        try {
            $bl = null;
            DB::transaction(function () use ($phpArray, &$bl, $file) {
                $bl = BlacklistLogs::create([
                    'file_name' => basename($file),
                    'bl_name_code' => 'pldp',
                    'bl_name' => 'Перечень лиц, групп, организаций...(ПЛПД)',
                    'status' => 'Ошибка в обработке',
                ]);


                $data = [];
                foreach ($phpArray['LegalizationPhysic'] as $key => $item) {
                    $data[$key]['first_name'] = $item['Name'];
                    $data[$key]['last_name'] = $item['Surname'];
                    $data[$key]['middle_name'] = $item['Patronomic'];
                    $data[$key]['birth_date'] = \Carbon\Carbon::parse($item['DataBirth']);
                    $data[$key]['BasicInclusion'] = $item['BasicInclusion'];
                    $data[$key]['type'] = 'plpd';
                    $data[$key]['country_id'] = 1;
                    $data[$key]['created_at'] = now();
                    $data[$key]['updated_at'] = now();
                    $data[$key]['blacklist_log_id'] = $bl->id;

                }
                $size = count($data);
                $inserted = \App\Models\BlackList::insert($data);

                $bl->status = "Успешно обработан {$size} записей";
                $bl->save();
                \App\Models\BlackList::where('type', 'pldp')->where('blacklist_log_id', '!=', $bl->id)->delete();
            });
            return new ImportLogResource($bl);
        } catch (\Exception $e) {
            return 'Error: ' . $e->getMessage();
        }
    }

    public function forall( $file)
    {
//        $file = public_path('blacklist') . '/63c915bc95a68.xml';

        $xmlString = file_get_contents($file);

        $xmlObject = simplexml_load_string($xmlString);

        $json = json_encode($xmlObject);

        $phpArray = json_decode($json, true);
        try {
            $bl = null;
            DB::transaction(function () use ($phpArray, &$bl, $file) {
                $bl = BlacklistLogs::create([
                    'file_name' => basename($file),
                    'bl_name_code' => 'forall',
                    'bl_name' => 'Сводный санкционный перечень Кыргызской Республики',
                    'status' => 'Ошибка в обработке',
                ]);
                $data = [];
                foreach ($phpArray['physicPersons']['KyrgyzPhysicPerson'] as $key => $item) {
                    $data[$key]['first_name'] = $item['Name'] ?: '';
                    $data[$key]['last_name'] = $item['Surname'] ?: '';
                    $data[$key]['middle_name'] = $item['Patronomic'] ?: '';
                    $data[$key]['PlaceBirth'] = $item['PlaceBirth'] ?: '';//"DataBirth" => "24.04.1975"
                    $data[$key]['birth_date'] = \Carbon\Carbon::parse(explode(',', $item['DataBirth'])[0])->format('Y-m-d');
                    $data[$key]['BasicInclusion'] = $item['BasicInclusion'] ?: '';
                    $data[$key]['type'] = 'forall';
                    $data[$key]['country_id'] = 1;
                    $data[$key]['created_at'] = now();
                    $data[$key]['updated_at'] = now();
                    $data[$key]['blacklist_log_id'] = $bl->id;
                }
                $size = count($data);
                $inserted = \App\Models\BlackList::insert($data);

                $bl->status = "Успешно обработан {$size} записей";
                $bl->save();
                \App\Models\BlackList::where('type', 'forall')->where('blacklist_log_id', '!=', $bl->id)->delete();
            });
            return new ImportLogResource($bl);
        } catch (\Exception $e) {
            return 'Error: ' . $e->getMessage();
        }
    }

    public function un( $file)
    {
//        $file = public_path('blacklist') . '/consolidated.xml';

        $xmlString = file_get_contents($file);

        $xmlObject = simplexml_load_string($xmlString);
//        dd($xmlObject);

        $json = json_encode($xmlObject);

        $phpArray = json_decode($json, true);

        try {
            $bl = null;
            $data = null;
            DB::transaction(function () use ($phpArray, &$bl, &$data,$file) {
                $bl = BlacklistLogs::create([
                    'file_name' => basename($file),
                    'bl_name_code' => 'un',
                    'bl_name' => 'Сводный санкционный перечень Совета Безопасности ООН',
                    'status' => 'Ошибка в обработке',
                ]);
                $data = [];
                foreach ($phpArray['INDIVIDUALS']['INDIVIDUAL'] as $key => $item) {
                    $data[$key]['first_name'] = (isset($item['FIRST_NAME']) && ($item['FIRST_NAME'])) ? $item['FIRST_NAME'] : '';
                    $data[$key]['last_name'] = (isset($item['THIRD_NAME']) && $item['THIRD_NAME']) ? $item['THIRD_NAME'] : '';
                    $data[$key]['middle_name'] = (isset($item['SECOND_NAME']) && $item['SECOND_NAME']) ? $item['SECOND_NAME'] : '';
                    $place = (isset($item['NATIONALITY']['VALUE']) && $item['NATIONALITY']['VALUE']) ? $item['NATIONALITY']['VALUE'] : '';//"DataBirth" => "24.04.1975"
                    $data[$key]['PlaceBirth'] = is_array($place) ? implode(',', $place) : $place;
                    $dob = null;
                    if (!isset($item['INDIVIDUAL_DATE_OF_BIRTH']['DATE'])) {
                        if (isset($item['INDIVIDUAL_DATE_OF_BIRTH']['YEAR'])) {
                            $dob = $item['INDIVIDUAL_DATE_OF_BIRTH']['YEAR'];
                        } else {
                            if (!isset($item['INDIVIDUAL_DATE_OF_BIRTH'][0]['DATE']) && isset($item['INDIVIDUAL_DATE_OF_BIRTH']['FROM_YEAR'])) {
                                $dob = $item['INDIVIDUAL_DATE_OF_BIRTH']['FROM_YEAR'];
                            }
                            if (isset($item['INDIVIDUAL_DATE_OF_BIRTH'][0]['DATE'])) {
                                $dob = $item['INDIVIDUAL_DATE_OF_BIRTH'][0]['DATE'];
                            }
                        }
                    } else {
                        $dob = $item['INDIVIDUAL_DATE_OF_BIRTH']['DATE'];
                    }
                    $data[$key]['birth_date'] = \Carbon\Carbon::parse($dob)->format('Y-m-d');
                    $data[$key]['type'] = 'un';
                    $data[$key]['created_at'] = now();
                    $data[$key]['updated_at'] = now();
                    $data[$key]['blacklist_log_id'] = $bl->id;
                }

                $size = count($data);

                $inserted = \App\Models\BlackList::insert($data);

                $bl->status = "Успешно обработан {$size} записей";
                $bl->save();
                \App\Models\BlackList::where('type', 'forall')->where('blacklist_log_id', '!=', $bl->id)->delete();
            });

            return new ImportLogResource($bl);
        } catch (\Exception $e) {
            return 'Error: ' . $e->getMessage();
        }
    }
}
