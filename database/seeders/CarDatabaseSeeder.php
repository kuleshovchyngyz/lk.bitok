<?php

namespace Database\Seeders;

use App\Jobs\UpdateCarDatabase;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use League\Csv\Reader;

class CarDatabaseSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tables = [
            'car_marks',
            'car_models',
            'car_generations',
            'car_series',
            'car_modifications',
            'car_characteristic_values',
        ];
        $MotoTables = [
            'car_mark',
            'car_model',
            'car_generation',
            'car_serie',
            'car_modification',
            'car_characteristic_value',
        ];

        foreach ($tables as $table) {
            if (file_exists( public_path('carData/' . $table . '.csv'))) {
                $seedData = $this->seedFromCSV(public_path('carData/' . $table . '.csv'), $table);
            }
        }
        foreach ($MotoTables as $table) {
            if (file_exists( public_path('Moto/' . $table . '.csv'))) {
                $seedData = $this->seedFromCSV(public_path('Moto/' . $table . '.csv'), $table.'s');
            }
        }
    }


    /**
     * Collect data from a given CSV file and return as array.
     *
     * @param $filename
     * @param string $delimitor
     * @param string $enclosure
     *
     * @return array|bool
     *
     * @internal param string $deliminator
     */
    public function seedFromCSV($filename, $table)
    {
        if (!file_exists($filename) || !is_readable($filename)) {
            return false;
        }

        $header = null;
        $data = [];
        $replace_values = [];
        $names = ['type', 'mark', 'model', 'generation', 'series', 'modification', 'characteristic_value'];
        foreach ($names as $name) {
            if ('id_car_type' != 'id_car_' . $name) {
                $replace_values['id_car_' . $name] = str_contains($filename, $name) ? 'id' : 'car_' . $name . '_id';
            }
        }

        $reader = Reader::createFromPath($filename)
            ->setHeaderOffset(0)
            ->setDelimiter(',')
            ->setEnclosure("'");


        $i = 0;
        foreach ($reader as $index => $row) {

            $i++;
            $header = $this->replaceArrayValues($row, $replace_values, $table);
            $header = $this->replaceNullString($header);
            $data[] = $header;
//                dump($table);
//            dd($header);
            if ($i % 1000 == 0) {
                UpdateCarDatabase::dispatch($data, $table);
                $data = [];
            }

        }

        UpdateCarDatabase::dispatch($data, $table);
        $data = [];


    }

    /**
     * Replace array values.
     *
     * @param array $array
     * @param array $replacement
     *
     * @return array
     */
    private function replaceArrayValues($array, $replacement, $table)
    {

        $result = [];
        foreach ($array as $key => $value) {
            if (($table == 'car_marks' || $table == 'car_models') && $key == 'id_car_type') {
                $result['car_type_id'] = $value;
            }elseif (($table == 'car_series') && $key == 'id_car_serie') {
                $result['id'] = $value;
            }elseif (isset($replacement[$key])) {
                $result[$replacement[$key]] = $value;
            } elseif ($key == 'id_car_characteristic') {
                $result['car_characteristic_id'] = $value;
            } elseif ($key == 'start_production_year') {
                $result['year_begin'] = $value;
            } elseif ($key == 'end_production_year') {
                $result['year_end'] = $value;
            } elseif ($key == 'id_car_serie') {
                $result['car_series_id'] = $value;
            } else {
                $result[$key] = $value;
            }
        }
        return $result;
    }

    /**
     * Replace NULL sting value with type null.
     *
     * @param array $array
     * @param array $timestampFields
     *
     * @return array
     */
    private function replaceNullString($array)
    {
        $result = [];

        foreach ($array as $key => $value) {
            if ($value == 'NULL') {
                $result[$key] = null;
            } else {
                $result[$key] = $value;
            }
        }
        return $result;
    }

    /**
     * Replace timestamp fields in array to datetime.
     *
     * @param array $array
     * @param array $timestampFields
     *
     * @return array
     */
    private function timestampsToDatetime($array, $timestampFields)
    {
        $result = [];

        foreach ($array as $key => $value) {
            if ((!empty($value)) && in_array($key, $timestampFields)) {
                $result[$key] = date('Y-m-d H:i:s', $value);
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }
}
