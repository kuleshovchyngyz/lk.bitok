<?php

namespace Database\Seeders;

use App\Models\LegalEntity;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LegalEntitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        LegalEntity::factory()->times(220)->create(
            [
                'birth_date' => '28/05/1990', // Adjust the date calculation as needed
            ]
        );
    }
}
