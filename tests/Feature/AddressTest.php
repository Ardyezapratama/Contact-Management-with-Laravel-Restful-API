<?php

namespace Tests\Feature;

use App\Models\Contact;
use Database\Seeders\ContactSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AddressTest extends TestCase
{
    public function testCreateSuccess(): void
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->post(
            "/api/contacts/{$contact->id}/addresses",
            [
                'street' => 'Test Street',
                'city' => 'test city',
                'province' => 'test province',
                'country' => 'test country',
                'postal_code' => '12345667'
            ],
            [
                'Authorization' => 'testToken'
            ]
        )->assertStatus(201)
            ->assertJson([
                'data' => [
                    'street' => 'Test Street',
                    'city' => 'test city',
                    'province' => 'test province',
                    'country' => 'test country',
                    'postal_code' => '12345667'
                ]
            ]);
    }

    public function testCreateFailed(): void
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->post(
            "/api/contacts/{$contact->id}/addresses",
            [
                'street' => 'Test Street',
                'city' => 'test city',
                'province' => 'test province',
                'country' => '',
                'postal_code' => '12345667'
            ],
            [
                'Authorization' => 'testToken'
            ]
        )->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'country' => [
                        "The country field is required."
                    ]
                ]
            ]);
    }

    public function testCreateContactNotFound(): void
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = 1;

        $this->post(
            "/api/contacts/{$contact}/addresses",
            [
                'street' => 'Test Street',
                'city' => 'test city',
                'province' => 'test province',
                'country' => 'test country',
                'postal_code' => '12345667'
            ],
            [
                'Authorization' => 'testToken'
            ]
        )->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => [
                        "not found"
                    ]
                ]
            ]);
    }
}
