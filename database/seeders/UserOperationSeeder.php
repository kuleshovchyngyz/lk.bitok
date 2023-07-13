<?php

namespace Database\Seeders;

use App\Models\UserOperation;
use Illuminate\Support\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserOperationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        UserOperation::factory()->times(400)->create(
            [
                'operation_date' => '28/05/1990 09:00', // Adjust the date calculation as needed
            ]
        );
    }
}