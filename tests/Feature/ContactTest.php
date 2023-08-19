<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Contact;
use Database\Seeders\UserSeeder;
use Illuminate\Support\Facades\DB;
use Database\Seeders\ContactSeeder;
use Illuminate\Support\Facades\Log;
use Database\Seeders\ContactsSeeder;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ContactTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        DB::delete('delete from addresses');
        DB::delete('delete from contacts');
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

    public function testShowSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $contact = Contact::query()->limit(1)->first();

        $this->get("/api/contacts/$contact->id", [
            "Authorization" => "test"
        ])
            ->assertStatus(200)
            ->assertJson([
                "data" => [
                    'first_name' => 'maya',
                    'last_name'  => 'm',
                    'email'      => 'maya@mail.com',
                    'phone'      => '111111',
                ]
            ]);
    }

    public function testContactNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $randomId = rand(1, 999);

        $this->get("/api/contacts/$randomId", [
            "Authorization" => "test"
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

    public function testShowSomeoneElseContact()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $contact = Contact::query()->limit(1)->first();

        $this->get("/api/contacts/$contact->id", [
            "Authorization" => "test2"
        ])
            ->assertStatus(401)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "unauthorized"
                    ]
                ]
            ]);
    }

    public function testUpdateSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $contact = Contact::query()->limit(1)->first();

        $this->put(
            "/api/contacts/$contact->id",
            [
                'first_name' => 'Yaya',
                'last_name'  => 'm',
                'email'      => 'maya@mail.com',
                'phone'      => '12331',
            ],
            [
                "Authorization" => "test"
            ]
        )
            ->assertStatus(200)
            ->assertJson([
                "data" => [
                    'first_name' => 'Yaya',
                    'last_name'  => 'm',
                    'email'      => 'maya@mail.com',
                    'phone'      => '12331',
                ]
            ]);
    }

    public function testValidateError()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $contact = Contact::query()->limit(1)->first();

        $this->put(
            "/api/contacts/$contact->id",
            [
                'first_name' => '',
                'last_name'  => 'm',
                'email'      => 'maya@mail.com',
                'phone'      => '',
            ],
            [
                "Authorization" => "test"
            ]
        )
            ->assertStatus(400)
            ->assertJson([
                "errors" => [
                    'first_name' => ['The first name field is required.'],
                    'phone'      => ['The phone field is required.'],
                ]
            ]);
    }

    public function testSuccessDelete()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $contact = Contact::query()->limit(1)->first();

        $this->delete(
            "/api/contacts/$contact->id",
            [],
            [
                "Authorization" => "test"
            ]
        )
            ->assertStatus(200)
            ->assertJson([
                'message' => "Contact remove successfuly"
            ]);
    }

    public function testDeleteNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $randomId = rand(1, 99);
        $this->delete(
            "/api/contacts/$randomId",
            [],
            [
                "Authorization" => "test"
            ]
        )
            ->assertStatus(404)
            ->assertJson([
                "errors" => [
                    'message' => [
                        "contact not found"
                    ]
                ]
            ]);
    }

    public function testSearchByFirstName()
    {
        $this->seed([UserSeeder::class, ContactsSeeder::class]);

        $response = $this->get('/api/contacts?name=first', [
            'Authorization' => 'test'
        ])
            ->assertStatus(200)
            ->json();

        self::assertEquals(10, count($response['data']));
        self::assertEquals(20, $response['meta']['total']);
    }

    public function testSearchByLastName()
    {
        $this->seed([UserSeeder::class, ContactsSeeder::class]);

        $response = $this->get('/api/contacts?name=last', [
            'Authorization' => 'test'
        ])
            ->assertStatus(200)
            ->json();

        self::assertEquals(10, count($response['data']));
        self::assertEquals(20, $response['meta']['total']);
    }

    public function testSearchNotFound()
    {
        $this->seed([UserSeeder::class, ContactsSeeder::class]);

        $response = $this->get('/api/contacts?name=salah', [
            'Authorization' => 'test'
        ])
            ->assertStatus(200)
            ->json();

        self::assertEquals(0, count($response['data']));
        self::assertEquals(0, $response['meta']['total']);
    }

    public function testSearchWithPage()
    {
        $this->seed([UserSeeder::class, ContactsSeeder::class]);

        $response = $this->get('/api/contacts?page=2', [
            'Authorization' => 'test'
        ])
            ->assertStatus(200)
            ->json();

        self::assertEquals(10, count($response['data']));
        self::assertEquals(20, $response['meta']['total']);
        self::assertEquals(2, $response['meta']['current_page']);
    }
}
