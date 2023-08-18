<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

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
}
