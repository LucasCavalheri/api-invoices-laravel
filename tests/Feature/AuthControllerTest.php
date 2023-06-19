<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function testLoginWithValidCredentials()
    {
        $password = $this->faker->password;
        $user = User::factory()->create([
            'password' => bcrypt($password),
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => $password,
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'token',
            ],
        ]);
    }

    public function testLoginWithInvalidCredentials()
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => $this->faker->email,
            'password' => $this->faker->password,
        ]);

        $response->assertStatus(401);
        $response->assertJson([
            'message' => 'Email or Password invalid',
            'status' => 401,
        ]);
    }

    public function testLogout()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('api/v1/auth/logout');

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Logged out',
            'status' => 200,
        ]);

        self::assertCount(0, $user->tokens);
    }
}
