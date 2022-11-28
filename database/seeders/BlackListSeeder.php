<?php

namespace Database\Seeders;

use App\Models\BlackList;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BlackListSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        BlackList::factory()->times(50)->create();
    }
}
