<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'name' => 'Manager',
            'email' => 'tester@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'Manager',
            'status' => 1,
        ]);
    }
}
