<?php

namespace Database\Seeders;

use App\Models\UserOperation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserOperationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        UserOperation::factory()->times(400)->create();
    }
}
