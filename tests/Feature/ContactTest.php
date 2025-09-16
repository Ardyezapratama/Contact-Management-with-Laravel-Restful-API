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
    public function testCreateContactSuccess(): void
    {
        $this->seed([UserSeeder::class]);
        $this->post(
            '/api/contacts',
            [
                'first_name' => 'Eza',
                'last_name' => 'Pratama',
                'email' => 'eza@gamil.com',
                'phone' => '08879378949'
            ],
            [
                'Authorization' => 'testToken'
            ]
        )->assertStatus(201)
            ->assertJson([
                'data' => [
                    'first_name' => 'Eza',
                    'last_name' => 'Pratama',
                    'email' => 'eza@gamil.com',
                    'phone' => '08879378949'
                ]
            ]);
    }

    public function testCreateContactFailed(): void
    {
        $this->seed([UserSeeder::class]);
        $this->post(
            '/api/contacts',
            [
                'first_name' => '',
                'last_name' => 'Pratama',
                'email' => 'ezapratama',
                'phone' => '08272739487398485948467488455444432330827273948739848594846748845544443233'
            ],
            [
                'Authorization' => 'testToken'
            ]
        )->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'first_name' => [
                        'The first name field is required.'
                    ],
                    'email' => [
                        'The email field must be a valid email address.'
                    ],
                    'phone' => [
                        'The phone field must not be greater than 20 characters.'
                    ]
                ]
            ]);
    }

    public function testCreateContactUnauthorize(): void
    {
        $this->seed([UserSeeder::class]);
        $this->post('/api/contacts', [
            'first_name' => 'Eza',
            'last_name' => 'Pratama',
            'email' => 'eza@gmail.com',
            'phone' => '0873674839748'
        ])->assertStatus(401)->assertJson([
                    'errors' => [
                        'message' => [
                            'unauthorized'
                        ]
                    ]
                ]);
    }

    public function testGetSuccess(): void
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();
        $this->get(uri: "/api/contacts/{$contact->id}", headers: [
            'Authorization' => 'testToken'
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'first_name' => 'Eza',
                    'last_name' => 'Pratama',
                    'email' => 'eza@gmail.com',
                    'phone' => '083678493784'
                ]
            ]);

    }

    public function testGetNotFound(): void
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->get('/api/contacts/' . ($contact->id + 1), [
            'Authorization' => 'testToken'
        ])->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'not found!'
                    ]
                ]
            ]);
    }

    public function testGetOthersContact(): void
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();
        $this->get(uri: "/api/contacts/{$contact->id}", headers: [
            'Authorization' => 'testToken2'
        ])->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'not found!'
                    ]
                ]
            ]);
    }

    public function testUpdateSuccess(): void
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();
        $this->put(
            "/api/contacts/{$contact->id}",
            [
                'first_name' => 'Ratri',
                'last_name' => 'Pratama',
                'email' => 'ezaratri@gmail.com',
                'phone' => '0836783345345'
            ],
            [
                'Authorization' => 'testToken'
            ]
        )->assertStatus(200)
            ->assertJson([
                'data' => [
                    'first_name' => 'Ratri',
                    'last_name' => 'Pratama',
                    'email' => 'ezaratri@gmail.com',
                    'phone' => '0836783345345'
                ]
            ]);
    }

    public function testUpdateValidationFailed(): void
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->put(
            "/api/contacts/{$contact->id}",
            [
                'first_name' => '',
                'last_name' => 'Pratama',
                'email' => 'ezaratri',
                'phone' => '0836783345345'
            ],
            [
                'Authorization' => 'testToken'
            ]
        )->assertStatus(400)
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

    public function testUpdateUnauthorized(): void
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();
        $this->put("/api/contacts/{$contact->id}", [
            'first_name' => '',
            'last_name' => 'Pratama',
            'email' => 'ezaratri',
            'phone' => '0836783345345'
        ])->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'unauthorized'
                    ]
                ]
            ]);
    }

    public function testDeleteSuccess(): void
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();
        $this->delete(
            uri: "/api/contacts/{$contact->id}",
            headers: [
                'Authorization' => 'testToken'
            ]
        )->assertStatus(200)
            ->assertJson([
                'data' => true
            ]);
    }

    public function testDeleteFailed(): void
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();
        $this->delete(
            uri: '/api/contacts/' . ($contact->id + 1),
            headers: [
                'Authorization' => 'testToken'
            ]
        )->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'not found!'
                    ]
                ]
            ]);
    }

    public function testSearchByFirstName(): void
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);
        $response = $this->get('/api/contacts?name=first', headers: [
            'Authorization' => 'testToken'
        ])
            ->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));
        self::assertEquals(10, count($response['data']));
        self::assertEquals(20, $response['meta']['total']);

    }

    public function testSearchByLastName(): void
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);
        $response = $this->get('/api/contacts?name=last', headers: [
            'Authorization' => 'testToken'
        ])->assertStatus(200)->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));
        self::assertEquals(10, count($response['data']));
        self::assertEquals(20, $response['meta']['total']);
    }

    public function testSearchByEmail(): void
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);
        $response = $this->get(uri: '/api/contacts?email=test1@gmail.com', headers: [
            'Authorization' => 'testToken'
        ])->assertStatus(200)->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));
        self::assertEquals(1, count($response['data']));
        self::assertEquals(1, $response['meta']['total']);
    }

    public function testSearchByPhone(): void
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);
        $response = $this->get(uri: '/api/contacts?phone=089757655', headers: [
            'Authorization' => 'testToken'
        ])->assertStatus(200)->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));
        self::assertEquals(1, count($response['data']));
        self::assertEquals(1, $response['meta']['total']);
    }

    public function testSearchWithPage(): void
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);
        $response = $this->get(uri: '/api/contacts?size=5&page=2', headers: [
            'Authorization' => 'testToken'
        ])->assertStatus(200)->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));
        self::assertEquals(5, count($response['data']));
        self::assertEquals(20, $response['meta']['total']);
        self::assertEquals(2, $response['meta']['current_page']);
    }

    public function testSearchNotFound(): void
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);
        $response = $this->get(uri: '/api/contacts?name=tidakada', headers: [
            'Authorization' => 'testToken'
        ])->assertStatus(200)->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));
        self::assertEquals(0, count($response['data']));
        self::assertEquals(0, $response['meta']['total']);

    }
}
