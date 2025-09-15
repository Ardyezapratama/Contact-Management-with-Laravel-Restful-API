<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
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

    public function testLoginSuccess(): void
    {
        $this->seed([UserSeeder::class]);
        $this->post('/api/users/login', [
            'username' => 'ezapratama',
            'password' => 'rahasia12345678',
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'username' => 'ezapratama',
                    'name' => 'Eza Pratama'
                ]
            ]);
        $user = User::where('username', 'ezapratama')->first();
        self::assertNotNull($user->token);
    }

    public function testLoginFailedUsernameWorng(): void
    {
        $this->seed([UserSeeder::class]);
        $this->post('/api/users/login', [
            'username' => 'ardyeza',
            'password' => 'rahasia12345678',
        ])->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => [
                        "username or password wrong!"
                    ]
                ]
            ]);
    }

    public function testLoginFailedPasswordWorng(): void
    {
        $this->seed([UserSeeder::class]);
        $this->post('/api/users/login', [
            'username' => 'ezapratama',
            'password' => 'rahasia1234',
        ])->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => [
                        "username or password wrong!"
                    ]
                ]
            ]);
    }

    public function testGetSuccess(): void
    {
        $this->seed([UserSeeder::class]);
        $this->get('/api/users/current', [
            'Authorization' => 'testToken'
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'username' => 'ezapratama',
                    'name' => 'Eza Pratama'
                ]
            ]);
    }

    public function testGetAnauthorized(): void
    {
        $this->seed([UserSeeder::class]);
        $this->get('/api/users/current')->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'unauthorized'
                    ]
                ]
            ]);
    }

    public function testGetInvalidToken(): void
    {
        $this->seed([UserSeeder::class]);
        $this->get('/api/users/current', [
            'Authorization' => 'tokenSalah'
        ])->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'unauthorized'
                    ]
                ]
            ]);
    }

    public function testUpdateNameSuccess()
    {
        $this->seed([UserSeeder::class]);
        $oldUser = User::where('username', 'ezapratama')->first();

        $this->patch(
            '/api/users/current',
            [
                'name' => 'ardyezapratama'
            ],
            [
                'Authorization' => 'testToken'
            ]
        )->assertStatus(200)
            ->assertJson([
                'data' => [
                    'username' => 'ezapratama',
                    'name' => 'ardyezapratama'
                ]
            ]);

        $newUser = User::where('username', 'ezapratama')->first();
        self::assertNotEquals($oldUser->name, $newUser->name);
    }

    public function testUpdatePasswordSuccess(): void
    {
        $this->seed([UserSeeder::class]);
        $oldUser = User::where('username', 'ezapratama')->first();

        $this->patch(
            '/api/users/current',
            [
                'password' => 'ezapratama123'
            ],
            [
                'authorization' => 'testToken'
            ]
        )->assertStatus(200)
            ->assertJson([
                'data' => [
                    'username' => 'ezapratama',
                    'name' => 'Eza Pratama'
                ]
            ]);

        $newUser = User::where('username', 'ezapratama')->first();
        self::assertNotEquals($oldUser->password, $newUser->password);
    }

    public function testUpdateFailed(): void
    {
        $this->seed([UserSeeder::class]);
        $this->patch('/api/users/current', [
            'name' => 'ardyezapratama'
        ])->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'unauthorized'
                    ]
                ]
            ]);
    }
}
