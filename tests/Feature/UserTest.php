<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function testRegisterSuccess(): void
    {
        $this->post('/api/users', [
            'username' => 'ezapratama',
            'password' => 'rahasia12345678',
            'name' => 'Eza Pratama'
        ])->assertStatus(201)
            ->assertJson([
                "data" => [
                    'username' => 'ezapratama',
                    'name' => 'Eza Pratama'
                ]
            ]);
    }

    public function testRegisterFailed(): void
    {
        $this->post('/api/users', [
            'username' => '',
            'passwrod' => '',
            'name' => ''
        ])->assertStatus(400)
            ->assertJson([
                "errors" => [
                    'username' => [
                        "The username field is required."
                    ],
                    'password' => [
                        "The password field is required."
                    ],
                    'name' => [
                        "The name field is required."
                    ]
                ]
            ]);
    }

    public function testRegisterUsernameAlreadyExists(): void
    {
        $this->testRegisterSuccess();
        $this->post('/api/users', [
            'username' => 'ezapratama',
            'password' => 'rahasia12345678',
            'name' => 'Eza Pratama'
        ])->assertStatus(400)
            ->assertJson([
                "errors" => [
                    'username' => [
                        'username already registered'
                    ]
                ]
            ]);

    }
}
