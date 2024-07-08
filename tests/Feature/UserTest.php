<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function testRegisterSuccess()
    {
        $this->post('api/users', [
            'username' => 'putricha',
            'password' => 'rahasia',
            'name' => 'Putri Norchasana'
        ])->assertStatus(201)
            ->assertJson(
                [
                    "data" => [
                        'username' => 'putricha',
                        'name' => 'Putri Norchasana'
                    ]
                ]
            );
    }
    public function testRegisterFailed()
    {
        $this->post('api/users', [
            'username' => '',
            'password' => '',
            'name' => ''
        ])->assertStatus(400)
            ->assertJson(
                [
                    "errors" => [
                        'username' => [
                            "The username field is required."
                        ],
                        'password' => [
                            "The password field is required."
                        ],
                        'name' => [
                            "The name field is required."
                        ],
                    ]
                ]
            );
    }
    public function testRegisterAlreadyExist()
    {
        $this->testRegisterSuccess();
        $this->post('api/users', [
            'username' => 'putricha',
            'password' => 'rahasia',
            'name' => 'Putri Norchasana'
        ])->assertStatus(400)
            ->assertJson(
                [
                    "errors" => [
                        'username' => [
                            "username already registered"
                        ],
                    ]
                ]
            );
    }

    public function testLoginSuccess()
    {
        $this->seed([UserSeeder::class]);
        $this->post('api/users/login', [
            'username' => 'doniwyk',
            'password' => 'doniwyk',
        ])->assertStatus(200)
            ->assertJson(
                [
                    "data" => [
                        'username' => 'doniwyk',
                        'name' => 'doniwyk'
                    ]
                ]
            );
        $user = User::where('username', 'doniwyk')->first();
        self::assertNotNull($user->token);
    }

    public function testLoginFailed()
    {
        $this->post('api/users/login', [
            'username' => 'doniwyk',
            'password' => 'doniwyk',
        ])->assertStatus(401)
            ->assertJson(
                [
                    'errors' => [
                        "message" => ["username or password wrong"]
                    ]
                ]
            );
    }
    public function testLoginPasswordWrong()
    {
        $this->seed([UserSeeder::class]);
        $this->post('api/users/login', [
            'username' => 'doniwyk',
            'password' => 'salah',
        ])->assertStatus(401)
            ->assertJson(
                [
                    'errors' => [
                        "message" => ["username or password wrong"]
                    ]
                ]
            );
    }


    public function testGetSuccess()
    {
        $this->seed([UserSeeder::class]);
        $this->get('api/users/current', [
            'Authorization' => 'test'
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'username' => 'doniwyk',
                    'name' => 'doniwyk',
                ]
            ]);
    }
    public function testGetUnAuthorized()
    {
        $this->seed([UserSeeder::class]);
        $this->get('api/users/current', [])->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'unauthorized'
                    ]
                ]
            ]);
    }

    public function testGetInvalidToken()
    {
        $this->seed([UserSeeder::class]);
        $this->get('api/users/current', [
            'Authorization' => 'testsalah'
        ])->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'unauthorized'
                    ]
                ]
            ]);
    }

    public function testUpdateNameSuccess()
    {
        $this->seed([UserSeeder::class]);
        $oldUser = User::where('username', 'doniwyk')->first();
        $this->patch(
            'api/users/current',
            [
                'name' => 'Putri',
            ],
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(200)
            ->assertJson([
                'data' => [
                    'username' => 'doniwyk',
                    'name' => 'Putri',
                ]
            ]);

        $newUser = User::where('username', 'doniwyk')->first();
        self::assertNotEquals($oldUser->name, $newUser->name);
    }
    public function testUpdatePasswordSuccess()
    {
        $this->seed([UserSeeder::class]);
        $oldUser = User::where('username', 'doniwyk')->first();
        $this->patch(
            'api/users/current',
            [
                'password' => 'baru',
            ],
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(200)
            ->assertJson([
                'data' => [
                    'username' => 'doniwyk',
                    'name' => 'doniwyk',
                ]
            ]);

        $newUser = User::where('username', 'doniwyk')->first();
        self::assertNotEquals($oldUser->password, $newUser->password);
    }

    public function testUpdateFailed()
    {
        $this->seed([UserSeeder::class]);
        $oldUser = User::where('username', 'doniwyk')->first();
        $this->patch(
            'api/users/current',
            [
                'name' => 'PutriPutriPutriPutriPutriPutriPutriPutriPutriPutriPutriPutriPutriPutriPutriPutriPutriPutriPutriPutriPutriPutriPutriPutri',
            ],
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'name' => [
                        "The name field must not be greater than 100 characters.",
                    ]
                ]
            ]);
    }

    public function testLogoutSuccess()
    {
        $this->seed([UserSeeder::class]);
        $this->delete(
            // 'api/users/logout',
            // [],
            // [
            //     'Authorization' => 'test'
            // ]
            uri: 'api/users/logout',
            headers: [
                'Authorization' => 'test'
            ]
        )->assertStatus(200)
            ->assertJson([
                'data' => true
            ]);

        $user = User::where('username', 'doniwyk')->first();
        self::assertNull($user->token);
    }

    public function testLogoutFailed()
    {
        $this->seed([UserSeeder::class]);
        $this->delete(
            'api/users/logout',
            [],
            [
                'Authorization' => 'salah'
            ]
        )->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'unauthorized'
                    ]
                ]
            ]);
    }
}
