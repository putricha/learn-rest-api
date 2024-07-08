<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'username' => 'doniwyk',
            'password' => Hash::make('doniwyk'),
            'name' => 'doniwyk',
            'token' => 'test'
        ]);
        User::create([
            'username' => 'doniwyk2',
            'password' => Hash::make('doniwyk2'),
            'name' => 'doniwyk2',
            'token' => 'test2'
        ]);
    }
}
