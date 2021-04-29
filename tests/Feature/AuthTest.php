<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * @group register
     * @group validation
     * @group authentication
     */
    public function testRegisterValidation()
    {
        $this->withHeaders(['Accept' => 'application/json'])
            ->post('api/auth/register')
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    /**
     * @group register
     * @group success
     * @group authentication
     */
    public function testRegisterSuccess()
    {
        $user = User::factory()->make();
        $payload = $user->only('name', 'email');
        $this->withHeaders(['Accept' => 'application/json'])
            ->post('api/auth/register', array_merge($payload, [
                'password' => '12345678',
            ]))
            ->assertCreated();
        $this->assertDatabaseHas($user->getTable(), $payload);
    }

    /**
     * @group login
     * @group authentication
     * @group failed
     */
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

    /**
     * @group login
     * @group authentication
     * @group success
     */
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

    /**
     * @group logout
     * @group authentication
     */
    public function testLogoutUnauthenticated()
    {
        $this->withHeaders(['Accept' => 'application/json'])
            ->post('api/auth/logout')
            ->assertUnauthorized();
    }

    /**
     * @group logout
     * @group authentication
     * @group success
     */
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
