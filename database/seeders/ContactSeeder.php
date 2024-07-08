<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where('username', 'doniwyk')->first();
        Contact::create([
            'first_name' => 'Doni',
            'last_name' => 'Wahyu',
            'email' => 'doniwyk@gmail.com',
            'phone' => '0987654321',
            'user_id' => $user->id
        ]);
    }
}
