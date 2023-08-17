<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class userTest extends TestCase
{
    public function testUserRegisterSuccess()
    {
        $this->post('/api/users/register', [
            'username' => 'hanasa',
            'name'     => 'hanasa',
            'password' => 'rahasia'
        ])
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
            ->assertJson([
                'data' => []
            ])
            ->assertStatus(400);
    }
}
