<?php

namespace Tests\Feature;

use App\Models\Contact;
use Database\Seeders\ContactSeeder;
use Database\Seeders\UserSeeder;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AddressTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        DB::delete('delete from addresses');
        DB::delete('delete from contacts');
        DB::delete('delete from users');
    }

    public function testCreateSuccess(): void
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $contact = Contact::query()->limit(1)->first();

        $this->post("/api/contacts/$contact->id/addresses", [
            'city'    => 'test',
            'country' => 'test'
        ], [
            'Authorization' => 'test'
        ])
            ->assertStatus(201)
            ->assertJson([
                'data' => [
                    'city'    => 'test',
                    'country' => 'test'
                ]
            ]);
    }

    public function testCreateFailed(): void
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $contact = Contact::query()->limit(1)->first();

        $this->post("/api/contacts/$contact->id/addresses", [
            'city'    => 'test',
            'country' => ''
        ], [
            'Authorization' => 'test'
        ])
            ->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'country' => [
                        'The country field is required.'
                    ]
                ]
            ]);
    }

    public function testContactNotFound(): void
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $randomId = rand(1, 109);

        $this->post("/api/contacts/$randomId/addresses", [
            'city'    => 'test',
            'country' => 'test'
        ], [
            'Authorization' => 'test'
        ])
            ->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'contact not found'
                    ]
                ]
            ]);
    }
}
