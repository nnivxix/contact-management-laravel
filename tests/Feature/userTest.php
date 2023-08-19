<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class userTest extends TestCase
{
    public function testUserRegisterSuccess()
    {
        $this->post('/api/users/register', [
            'username' => 'hanasa',
            'name'     => 'hanasa',
            'password' => 'rahasia'
        ])
            ->assertStatus(201)
            ->assertJson([
                'data' => [
                    'username' => 'hanasa',
                    'name'     => 'hanasa',
                ]
            ]);
    }

    public function testUserRegisterFailed()
    {
        $this->post('/api/users/register', [
            'username' => '',
            'name'     => '',
            'password' => ''
        ])
            ->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'username' => ['The username field is required.'],
                    'name'     => ['The name field is required.'],
                    'password' => ['The password field is required.']

                ]
            ]);
    }

    public function testUsernameAlreadyExist()
    {
        $this->testUserRegisterSuccess();
        $this->post('/api/users/register', [
            'username' => 'hanasa',
            'name'     => 'hanasa',
            'password' => 'rahasia'
        ])
            ->assertStatus(400)
            ->assertJson([
                "errors" => [
                    'username' => [
                        "The username has already been taken."
                    ]
                ]
            ]);
    }

    public function testLoginSucces()
    {
        $this->seed([UserSeeder::class]);
        $this->post('/api/users/login', [
            'username' => 'hanasa',
            'password' => 'password'
        ])
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'username' => 'hanasa',
                    'name' => 'hanasa'
                ]
            ]);

        $user = User::where('username', 'hanasa')->first();
        self::assertNotNull($user->token);
    }

    public function testLoginUsernameWrong()
    {
        $this->seed([UserSeeder::class]);
        $this->post('/api/users/login', [
            'username' => 'han',
            'password' => 'password'
        ])
            ->assertStatus(401)
            ->assertJson([
                'errors' => [
                    "message" => [
                        "username or password wrong"
                    ]
                ]
            ]);
    }

    public function testLoginPasswordWrong()
    {
        $this->seed([UserSeeder::class]);
        $this->post('/api/users/login', [
            'username' => 'hanasa',
            'password' => 'salah'
        ])
            ->assertStatus(401)
            ->assertJson([
                'errors' => [
                    "message" => [
                        "username or password wrong"
                    ]
                ]
            ]);
    }

    public function testGetCurrentUser()
    {
        $this->seed([UserSeeder::class]);

        $this->get('/api/users/current', [
            'Authorization' => 'test'
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'username' => 'hanasa',
                    'name'     => 'hanasa'
                ]
            ]);
    }

    public function testUnauthorizedCurrentUser()
    {
        $this->seed([UserSeeder::class]);

        // without token
        $this->get('/api/users/current')
            ->assertStatus(401)
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

        $this->get('/api/users/current', [
            'Authorization' => 'salah'
        ])->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'unauthorized'
                    ]
                ]
            ]);
    }

    public function testUpdateNameSucces()
    {
        $this->seed([UserSeeder::class]);

        $oldUser = User::where('username', 'hanasa')->first();

        $this->put(
            '/api/users/current',
            [
                'name' => 'Maya'
            ],
            [
                'Authorization' => 'test'
            ]
        )
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'username' => 'hanasa',
                    'name'     => 'Maya'
                ]
            ]);

        $newUser = User::where('username', 'hanasa')->first();
        $this->assertNotEquals($oldUser->name, $newUser->name);
    }

    public function testUpdatePasswordSucces()
    {
        $this->seed([UserSeeder::class]);

        $oldUser = User::where('username', 'hanasa')->first();

        $this->put(
            '/api/users/current',
            [
                'password' => Hash::make('baru')
            ],
            [
                'Authorization' => 'test'
            ]
        )
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'username' => 'hanasa',
                    'name'     => 'hanasa'
                ]
            ]);

        $newUser = User::where('username', 'hanasa')->first();
        $this->assertNotEquals($oldUser->password, $newUser->password);
    }
}
