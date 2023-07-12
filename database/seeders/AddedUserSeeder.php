<?php

namespace Database\Seeders;

use App\Models\AddedUser;
use Illuminate\Support\Carbon;
use Illuminate\Database\Seeder;
use Database\Factories\AddedUserFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AddedUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        AddedUser::factory()->times(200)->create(
            [
                'birth_date' => '1990-05-28 00:00:00', // Adjust the date calculation as needed
            ]
        );
    }
}
