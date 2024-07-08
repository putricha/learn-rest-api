<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Contact;
use Database\Seeders\AddressSeeder;
use Database\Seeders\ContactSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class AddressTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function testCreateSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $contact = Contact::query()->limit(1)->first();
        $response = $this->post(
            '/api/contacts/' . $contact->id . '/addresses',
            [
                'street' => 'donomulyo',
                'city' => 'Malang',
                'province' => 'Jawa Timur',
                'country' => 'Indonesia',
                'postal_code' => '65167'
            ],
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(201)
            ->assertJson([
                'data' => [
                    'street' => 'donomulyo',
                    'city' => 'Malang',
                    'province' => 'Jawa Timur',
                    'country' => 'Indonesia',
                    'postal_code' => '65167'
                ]
            ]);
    }
    public function testCreateFailed()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();
        $this->post(
            '/api/contacts/' . $contact->id . '/addresses',
            [
                'street' => 'donomulyo',
                'city' => 'Malang',
                'province' => 'Jawa Timur',
                'country' => '',
                'postal_code' => '65167'
            ],
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'country' => [
                        'The country field is required.',
                    ]
                ]
            ]);
    }
    public function testCreateContactNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();
        $this->post(
            '/api/contacts/' . $contact->id + 1 . '/addresses',
            [
                'street' => 'donomulyo',
                'city' => 'Malang',
                'province' => 'Jawa Timur',
                'country' => 'Indonesia',
                'postal_code' => '65167'
            ],
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'not found'
                    ]
                ]
            ]);
    }

    public function testGetSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->limit(1)->first();
        $url = '/api/contacts/' . $address->contact_id . '/addresses/' . $address->id;
        // $this->get('/api/contacts/' . $address->contact_id . '/addresses' .'/'. $address->id, [
        $response = $this->get($url, [
            'Authorization' => 'test'
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'street' => 'donomulyo',
                    'city' => 'Malang',
                    'province' => 'Jawa Timur',
                    'country' => 'Indonesia',
                    'postal_code' => '65167'
                ]
            ]);
        Log::info(json_encode($response, JSON_PRETTY_PRINT));
    }
    public function testGetNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->limit(1)->first();
        $url = '/api/contacts/' . $address->contact_id . '/addresses/' . ($address->id + 1);


        // $this->get('/api/contacts/' . $address->contact_id . '/addresses' .'/'. $address->id, [
        $response = $this->get($url, [
            'Authorization' => 'test'
        ])->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'not found'
                    ]
                ]
            ]);
        Log::info(json_encode($response, JSON_PRETTY_PRINT));
    }


    public function testUpdateSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->limit(1)->first();
        $this->put('/api/contacts/' . $address->contact_id . '/addresses/' . $address->id, [
            'street' => 'Semanggi',
            'city' => 'Surabaya',
            'province' => 'Jawa Timur',
            'country' => 'Indonesia',
            'postal_code' => '65168'
        ], [
            'Authorization' => 'test'
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'street' => 'Semanggi',
                    'city' => 'Surabaya',
                    'province' => 'Jawa Timur',
                    'country' => 'Indonesia',
                    'postal_code' => '65168'
                ]
            ]);
    }



    public function testUpdateFailed()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->limit(1)->first();
        $this->put('/api/contacts/' . $address->contact_id . '/addresses/' . $address->id, [
            'street' => 'Semanggi',
            'city' => 'Surabaya',
            'province' => 'Jawa Timur',
            'country' => '',
            'postal_code' => '65168'
        ], [
            'Authorization' => 'test'
        ])->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'country' => [
                        'The country field is required.',
                    ]
                ]
            ]);
    }
    public function testUpdateNotFound()
    {

        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->limit(1)->first();
        $this->put('/api/contacts/' . $address->contact_id . '/addresses/' . ($address->id + 1), [
            'street' => 'Semanggi',
            'city' => 'Surabaya',
            'province' => 'Jawa Timur',
            'country' => 'Indonesia',
            'postal_code' => '65168'
        ], [
            'Authorization' => 'test'
        ])->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'not found',
                    ]
                ]
            ]);
    }
    public function testDeleteSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->limit(1)->first();
        $this->delete('/api/contacts/' . $address->contact_id . '/addresses/' . $address->id, [],  [
            'Authorization' => 'test'
        ])->assertStatus(200)
            ->assertJson([
                'data' => true
            ]);
    }

    public function testDeleteNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->limit(1)->first();
        $this->delete('/api/contacts/' . $address->contact_id . '/addresses/' . ($address->id + 1), [],  [
            'Authorization' => 'test'
        ])->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'not found',
                    ]
                ]
            ]);
    }

    public function testListSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $contact = Contact::query()->limit(1)->first();
        $this->get('/api/contacts/' . $contact->id . '/addresses/', [
            'Authorization' => 'test'
        ])->assertStatus(200)
            ->assertJson([
                'data' => [[
                    'street' => 'donomulyo',
                    'city' => 'Malang',
                    'province' => 'Jawa Timur',
                    'country' => 'Indonesia',
                    'postal_code' => '65167'
                ]]
            ]);
    }
    public function testListContactNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $contact = Contact::query()->limit(1)->first();
        $this->get('/api/contacts/' . ($contact->id + 1) . '/addresses/', [
            'Authorization' => 'test'
        ])->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'not found'
                    ]
                ]
            ]);
    }
}
