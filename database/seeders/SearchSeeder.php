<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SearchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run()
    {
        $user = User::where('username', 'doniwyk')->first();
        for ($i = 0; $i < 20; $i++) {
            Contact::create(
                [
                    'first_name' => 'Putri ' . $i,
                    'last_name' => 'Chasana ' . $i,
                    'email' => 'putricha' . $i . '@gmail.com',
                    'phone' => '11111' . $i,
                    'user_id' => $user->id
                ]
            );
        }
    }
}
