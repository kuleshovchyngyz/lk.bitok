<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ImportLogResource;
use App\Models\BlackList;
use App\Models\BlacklistLogs;
use App\Models\BlackListsLegalEntity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use SimpleXMLElement;

class ImportController extends Controller
{
    public function upload(Request $request)
    {
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = $file->getClientOriginalName();
            $file->move(public_path('blacklist'), $filename);
            $url = public_path('blacklist') . '/' . $filename;
            $type = $request->get('type');
            return call_user_func_array([$this, $type], [$url]);
        }
        return 'no file found';
    }

    public function import()
    {
        $logs = ImportLogResource::collection(BlacklistLogs::orderBy('created_at', 'desc')->get());
        $values = [
            'pft' => 'Перечень физических лиц...(ПФТ)',
            'plpd' => 'Перечень лиц, групп, организаций...(ПЛПД)',
            'forall' => 'Сводный санкционный перечень Кыргызской Республики',
            'un' => 'Сводный санкционный перечень Совета Безопасности ООН',
            'plpdLegal' => 'Перечень юридических лиц...(ПЛПД)',
        ];
        return compact('logs', 'values');
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
            DB::transaction(function () use ($phpArray, &$bl, $file,) {
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
                $addedUsers = BlackList::all();
                foreach ($addedUsers as $addedUser) {
                    $addedUser->hash = md5(trim($addedUser['last_name'] ?? null) . trim($addedUser['first_name'] ?? null) . trim($addedUser['middle_name'] ?? null) . trim($addedUser->birth_date->format('d/m/Y') ?? null));
                    $addedUser->save();
                }
            });
            return new ImportLogResource($bl);
        } catch (\Exception $e) {
            return 'Error: ' . $e->getMessage();
        }

    }

    public function plpdLegal($file)
    {
        return $this->Legals($file, 'plpdLegal', 'Перечень юридических лиц...(ПЛПД)');
    }

    public function plpd($file)
    {

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
                $addedUsers = BlackList::all();
                foreach ($addedUsers as $addedUser) {
                    $addedUser->hash = md5(trim($addedUser['last_name'] ?? null) . trim($addedUser['first_name'] ?? null) . trim($addedUser['middle_name'] ?? null) . trim($addedUser->birth_date->format('d/m/Y') ?? null));
                    $addedUser->save();
                }
            });
            return new ImportLogResource($bl);
        } catch (\Exception $e) {
            return 'Error: ' . $e->getMessage();
        }
    }

    public function forall($file)
    {

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
                    'bl_name' => 'Сводный санкционный перечень Кыргызской Республики и Сводный санкционный перечень юридических лиц Кыргызской Республики',
                    'status' => 'Ошибка в обработке',
                ]);
                $this->Legals($file, 'pftLegals', 'Сводный санкционный перечень юридических лиц Кыргызской Республики');
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
                $addedUsers = BlackList::all();
                foreach ($addedUsers as $addedUser) {
                    $addedUser->hash = md5(trim($addedUser['last_name'] ?? null) . trim($addedUser['first_name'] ?? null) . trim($addedUser['middle_name'] ?? null) . trim($addedUser->birth_date->format('d/m/Y') ?? null));
                    $addedUser->save();
                }
            });
            return new ImportLogResource($bl);
        } catch (\Exception $e) {
            return 'Error: ' . $e->getMessage();
        }
    }

    public function un($file)
    {
        $xmlString = file_get_contents($file);

        $xmlObject = simplexml_load_string($xmlString);
//        dd($xmlObject);

        $json = json_encode($xmlObject);

        $phpArray = json_decode($json, true);

        try {
            $bl = null;
            $data = null;
            DB::transaction(function () use ($phpArray, &$bl, &$data, $file) {
                $bl = BlacklistLogs::create([
                    'file_name' => basename($file),
                    'bl_name_code' => 'un',
                    'bl_name' => 'Сводный санкционный перечень Совета Безопасности ООН и Сводный санкционный перечень юридических лиц Совета Безопасности ООН',
                    'status' => 'Ошибка в обработке',
                ]);
                $this->Legals($file, 'unLegals', 'Сводный санкционный перечень юридических лиц Совета Безопасности ООН');
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
                $addedUsers = BlackList::all();
                foreach ($addedUsers as $addedUser) {
                    $addedUser->hash = md5(trim($addedUser['last_name'] ?? null) . trim($addedUser['first_name'] ?? null) . trim($addedUser['middle_name'] ?? null) . trim($addedUser->birth_date->format('d/m/Y') ?? null));
                    $addedUser->save();
                }
            });

            return new ImportLogResource($bl);
        } catch (\Exception $e) {
            return 'Error: ' . $e->getMessage();
        }
    }

    public function Legals($file, $type, $text)
    {
        return DB::transaction(function () use ($file, $type, $text) {
            $xmlData = file_get_contents($file);
            $xml = new SimpleXMLElement($xmlData);

            $bl = BlacklistLogs::create([
                'file_name' => basename($file),
                'bl_name_code' => $type,
                'bl_name' => $text,
                'status' => 'Ошибка в обработке',
            ]);

            $count = 0;

            if ($xml->getName() === 'CONSOLIDATED_LIST') {
                foreach ($xml->ENTITIES->ENTITY as $entity) {
                    $name = (string) $entity->FIRST_NAME;

                    if (!empty($name)) {
                        $count++;
                        $hash = md5(trim($name));
                        $blackList = new BlackListsLegalEntity();
                        $blackList->name = $name;
                        $blackList->type = $type;
                        $blackList->hash = $hash;
                        $blackList->blacklist_log_id = $bl->id;
                        $blackList->save();
                    }
                }
            } elseif ($xml->getName() === 'SanctionList') {
                foreach ($xml->legalPersons->KyrgyzLegalPerson as $person) {
                    $name = (string) $person->Name;

                    if (!empty($name)) {
                        $count++;
                        $hash = md5(trim($name));
                        $blackList = new BlackListsLegalEntity();
                        $blackList->name = $name;
                        $blackList->type = $type;
                        $blackList->hash = $hash;
                        $blackList->blacklist_log_id = $bl->id;
                        $blackList->save();
                    }
                }
            } elseif ($xml->getName() === 'ArrayOfLegalization') {
                foreach ($xml->Legalization as $legalization) {
                    $name = (string) $legalization->Name;
                    $address = (string) $legalization->City.', '.(string) $legalization->Street;

                    if (!empty($name)) {
                        $count++;
                        $hash = md5(trim($name));
                        $blackList = new BlackListsLegalEntity();
                        $blackList->name = $name;
                        $blackList->address = strlen($address) > 5 ? $address : '';
                        $blackList->type = $type;
                        $blackList->hash = $hash;
                        $blackList->blacklist_log_id = $bl->id;
                        $blackList->save();
                    }
                }
            }

            BlackListsLegalEntity::where('type', $type)
                ->where('blacklist_log_id', '!=', $bl->id)
                ->delete();

            $bl->status = "Успешно обработано {$count} записей";
            $bl->save();

            return new ImportLogResource($bl);
        });
    }



}
