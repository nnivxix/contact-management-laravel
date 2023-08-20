<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Address;
use App\Models\Contact;
use Database\Seeders\UserSeeder;
use Illuminate\Support\Facades\DB;
use Database\Seeders\AddressSeeder;
use Database\Seeders\ContactSeeder;
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

    public function testGetAddressContact(): void
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $contact = Contact::query()->limit(1)->first();
        $this->get("/api/contacts/$contact->id/addresses/", [
            "Authorization" => 'test'
        ])
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    "city"    => "test",
                    "country" => "test"
                ]
            ]);
    }

    public function testGetAddressContactButContactNotYetCreated(): void
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $randomId = rand(1, 99);
        $this->get("/api/contacts/$randomId/addresses/", [
            "Authorization" => 'test'
        ])
            ->assertStatus(404)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "contact not found"
                    ]
                ]
            ]);
    }

    public function testGetAddressContactButAddressNotYetCreated(): void
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);

        $contact = Contact::query()->limit(1)->skip(1)->first();

        $this->get("/api/contacts/$contact->id/addresses/", [
            "Authorization" => 'test'
        ])
            ->assertStatus(404)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "address not yet created"
                    ]
                ]
            ]);
    }

    public function testUpdateSuccess(): void
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);

        $contact = Contact::query()->limit(1)->first();

        $this->put("/api/contacts/$contact->id/addresses", [
            'city'    => 'update',
            'country' => 'update'
        ], [
            'Authorization' => 'test'
        ])
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'city'    => 'update',
                    'country' => 'update'
                ]
            ]);
    }

    public function testUpdateFailed(): void
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);

        $contact = Contact::query()->limit(1)->first();

        $this->put("/api/contacts/$contact->id/addresses", [
            'city'    => 'update',
            'country' => ''
        ], [
            'Authorization' => 'test'
        ])
            ->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'country' => ["The country field is required."]
                ]
            ]);
    }

    public function testUpdateNotFound(): void
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);

        $contact = Contact::query()->limit(1)->first();

        $this->put("/api/contacts/$contact->id/addresses", [
            'city'    => 'update',
            'country' => ''
        ], [
            'Authorization' => 'test'
        ])
            ->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'country' => ["The country field is required."]
                ]
            ]);
    }

    public function testUpdateOnContactDoesnotHaveAnAddress(): void
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);

        $contact = Contact::query()->skip(1)->limit(1)->first();

        $this->put("/api/contacts/$contact->id/addresses", [
            'city'    => 'baru',
            'country' => 'baru'
        ], [
            'Authorization' => 'test'
        ])
            ->assertStatus(201)
            ->assertJson([
                'data' => [
                    'city'    => 'baru',
                    'country' => 'baru'
                ]
            ]);
    }
}
