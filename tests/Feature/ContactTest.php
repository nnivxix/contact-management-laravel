<?php

namespace Tests\Feature;

use Tests\TestCase;
use Database\Seeders\UserSeeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ContactTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        DB::delete('delete from contacts');
        DB::delete('delete from addresses');
        DB::delete('delete from users');
    }
    public function testCreateSuccess()
    {
        $this->seed([UserSeeder::class]);

        $this->post(
            '/api/contacts',
            [
                'first_name' => 'maya',
                'last_name'  => 'maya',
                'email'      => 'maya@mail.com',
                'phone'      => '9867',
            ],
            [
                'Authorization' => 'test'
            ]
        )
            ->assertStatus(201)
            ->assertJson([
                'data' => [
                    'first_name' => 'maya',
                    'last_name'  => 'maya',
                    'email'      => 'maya@mail.com',
                    'phone'      => '9867',
                ]
            ]);
    }

    public function testCreateFailed()
    {
        $this->seed([UserSeeder::class]);

        $this->post(
            '/api/contacts',
            [
                'last_name'  => 'maya',
                'email'      => 'mayamail.com',
                'phone'      => '9867',
            ],
            [
                'Authorization' => 'test'
            ]
        )
            ->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'first_name' => ['The first name field is required.'],
                    'email'      => ['The email field must be a valid email address.'],
                ]
            ]);
    }
}
