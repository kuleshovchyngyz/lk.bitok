<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $countries = [
            ["name" => "Afghanistan", "sanction" => 2],
            ["name" => "Åland Islands", "sanction" => 0],
            ["name" => "Albania", "sanction" => 0],
            ["name" => "Algeria", "sanction" => 1],
            ["name" => "American Samoa", "sanction" => 0],
            ["name" => "Andorra", "sanction" => 0],
            ["name" => "Angola", "sanction" => 1],
            ["name" => "Anguilla", "sanction" => 0],
            ["name" => "Antarctica", "sanction" => 0],
            ["name" => "Antigua and Barbuda", "sanction" => 0],
            ["name" => "Argentina", "sanction" => 1],
            ["name" => "Armenia", "sanction" => 2],
            ["name" => "Aruba", "sanction" => 0],
            ["name" => "Australia", "sanction" => 0],
            ["name" => "Austria", "sanction" => 0],
            ["name" => "Azerbaijan", "sanction" => 2],
            ["name" => "Bahamas", "sanction" => 0],
            ["name" => "Bahrain", "sanction" => 2],
            ["name" => "Bangladesh", "sanction" => 2],
            ["name" => "Barbados", "sanction" => 0],
            ["name" => "Belarus", "sanction" => 0],
            ["name" => "Belgium", "sanction" => 0],
            ["name" => "Belize", "sanction" => 0],
            ["name" => "Benin", "sanction" => 1]
        ];
        Country::insert($countries);
    }
}
