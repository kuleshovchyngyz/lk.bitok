<?php

namespace Database\Seeders;

use App\Models\AddedUser;
use Database\Factories\AddedUserFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AddedUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        AddedUser::factory()->times(1000)->create();
    }
}
