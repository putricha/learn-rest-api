<?php

namespace Tests\Feature;

use App\Models\Contact;
use Database\Seeders\ContactSeeder;
use Database\Seeders\SearchSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class ContactTest extends TestCase
{
    /**
     * A basic feature test example.
     */

    public function testCreateSuccess()
    {
        $this->seed([UserSeeder::class]);
        $this->post('api/contacts', [
            'first_name' => 'Doni',
            'last_name' => 'Wahyu',
            'email' => 'doniwyk@gmail.com',
            'phone' => '0987654321'
        ], [
            'Authorization' => 'test'
        ])->assertStatus(201)
            ->assertJson([
                'data' => [
                    'first_name' => 'Doni',
                    'last_name' => 'Wahyu',
                    'email' => 'doniwyk@gmail.com',
                    'phone' => '0987654321'
                ]
            ]);
    }
    public function testCreateFailed()
    {
        $this->seed([UserSeeder::class]);
        $this->post('api/contacts', [
            'first_name' => '',
            'last_name' => 'Wahyu',
            'email' => 'doniwyk',
            'phone' => '0987654321'
        ], [
            'Authorization' => 'test'
        ])->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'first_name' => [
                        'The first name field is required.'
                    ],
                    'email' => [
                        'The email field must be a valid email address.'
                    ],
                ]
            ]);
    }
    public function testCreateUnauthorized()
    {
        $this->seed([UserSeeder::class]);
        $this->post('api/contacts', [
            'first_name' => '',
            'last_name' => 'Wahyu',
            'email' => 'doniwyk',
            'phone' => '0987654321'
        ], [
            'Authorization' => 'wrong'
        ])->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'unauthorized'
                    ]
                ]
            ]);
    }

    public function testGetSucces()
    {
        $this->seed([UserSeeder::class]);
        $this->seed([ContactSeeder::class]);
        $contact = Contact::query()->Limit(1)->first();
        $this->get(
            'api/contacts/' . $contact->id,
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(200)
            ->assertJson([
                'data' => [
                    'first_name' => 'Doni',
                    'last_name' => 'Wahyu',
                    'email' => 'doniwyk@gmail.com',
                    'phone' => '0987654321'
                ]
            ]);
    }
    public function testGetNotFound()
    {

        $this->seed([UserSeeder::class]);
        $this->seed([ContactSeeder::class]);
        $contact = Contact::query()->Limit(1)->first();
        $this->get(
            'api/contacts/' . ($contact->id + 1),
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
    public function testGetOtherContact()
    {
        $this->seed([UserSeeder::class]);
        $this->seed([ContactSeeder::class]);
        $contact = Contact::query()->Limit(1)->first();
        $this->get(
            'api/contacts/' . $contact->id,
            [
                'Authorization' => 'test2'
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

    public function testUpdateSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->Limit(1)->first();
        $this->put(
            'api/contacts/' . $contact->id,
            [
                'first_name' => 'Putri',
                'last_name' => 'Chasana',
                'email' => 'putricha@gmail.com',
                'phone' => '09876543212'
            ],
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(200)
            ->assertJson([
                'data' => [
                    'first_name' => 'Putri',
                    'last_name' => 'Chasana',
                    'email' => 'putricha@gmail.com',
                    'phone' => '09876543212'
                ]
            ]);
    }

    public function testUpdateValidationError()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->Limit(1)->first();
        $this->put(
            'api/contacts/' . $contact->id,
            [
                'first_name' => '',
                'last_name' => 'Chasana',
                'email' => 'putricha@gmail.com',
                'phone' => '09876543212'
            ],
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'first_name' => [
                        'The first name field is required.'
                    ]
                ]
            ]);
    }

    public function testDeleteSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->Limit(1)->first();
        $this->delete(
            '/api/contacts/' . $contact->id,
            [],
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(200)
            ->assertJson([
                'data' => true
            ]);
    }

    public function testDeleteNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->Limit(1)->first();
        $this->delete(
            '/api/contacts/' . ($contact->id + 1),
            [],
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

    public function testSearchByFirstName()
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);
        $response = $this->get('api/contacts?name=Putri', [
            'Authorization' => 'test'
        ])
            ->assertStatus(200)
            ->json();

        Log::info(json_encode($response));
        self::assertEquals(10, count($response['data']));
    }
    public function testSearchByLastName()
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);
        $response = $this->get('api/contacts?name=Chasana', [
            'Authorization' => 'test'
        ])
            ->assertStatus(200)
            ->json();

        Log::info(json_encode($response));
        self::assertEquals(10, count($response['data']));
    }
    public function testSearchByEmail()
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);
        $response = $this->get('api/contacts?email=putricha', [
            'Authorization' => 'test'
        ])
            ->assertStatus(200)
            ->json();

        Log::info(json_encode($response));
        self::assertEquals(10, count($response['data']));
    }
    public function testSearchByPhone()
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);
        $response = $this->get('api/contacts?phone=11111', [
            'Authorization' => 'test'
        ])
            ->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));
        self::assertEquals(10, count($response['data']));
        self::assertEquals(20, ($response['meta']['total']));
    }
    public function testSearchNotFound()
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);
        $response = $this->get('api/contacts?name=Doni', [
            'Authorization' => 'test'
        ])
            ->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));
        self::assertEquals(10, count($response['data']));
        self::assertEquals(20, ($response['meta']['total']));
    }
    public function testSearchWithPage()
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);
        $response = $this->get('api/contacts?size=5&page=2', [
            'Authorization' => 'test'
        ])
            ->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));
        self::assertEquals(5, count($response['data']));
        self::assertEquals(20, $response['meta']['total']);
        self::assertEquals(2, $response['meta']['current_page']);
    }
}
