<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Enterprise;
use App\Models\Store;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;

class AuthTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function testRegisterValidation()
    {
        $this->withHeaders(['Accept' => 'application/json'])
            ->post('api/auth/register')
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    public function testRegisterSuccess()
    {
        $user = User::factory()->make();
        $payload = $user->only('name','email');
        $this->withHeaders(['Accept' => 'application/json'])
            ->post('api/auth/register', array_merge($payload,[
                'password' => '12345678',
            ]))
            ->assertCreated();
        $this->assertDatabaseHas($user->getTable(), $payload);
    }

    public function testLoginFailed()
    {
        $this->withHeaders(['Accept' => 'application/json'])
            ->post('api/auth/login', [
                'email' => $this->faker->email,
                'password' => '12345678',
            ])
            ->assertOk()
            ->assertJsonPath('token', null);
    }

    public function testLoginSuccess()
    {
        $password = '12345678';
        $user = User::factory()->create(['password' => $password]);
        $payload = $user->only($user->getFillable());
        $this->withHeaders(['Accept' => 'application/json'])
            ->post('api/auth/login', array_merge($payload, [
                'password' => $password,
            ]))
            ->assertOk()
            ->assertJsonPath('token', $user->currentAccessToken());
    }

    public function testLogoutUnauthenticated()
    {
        $this->withHeaders(['Accept' => 'application/json'])
            ->post('api/auth/logout')
            ->assertUnauthorized();
    }

    public function testLogoutSuccess()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $this->withHeaders(['Accept' => 'application/json'])
            ->post('api/auth/logout')
            ->assertOk();
        $token = $user->currentAccessToken()->plainTextToken;
        $this->assertEquals($token, null);
    }
}
