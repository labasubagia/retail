<?php

namespace Tests\Feature;

use App\Models\Enterprise;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class EnterpriseTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function testPaginateUnauthenticated()
    {
        $this->withHeaders(['Accept' => 'application/json'])
            ->get('api/enterprise')
            ->assertUnauthorized();
    }

    public function testPaginateSuccess()
    {
        $count = 20;
        $user = User::factory()->create(['enterprise_id' => null, 'store_id' => null]);
        Enterprise::factory($count)->create();
        Sanctum::actingAs($user);

        $this->assertDatabaseCount((new Enterprise)->getTable(), $count);
        $response =$this->withHeaders(['Accept' => 'application/json'])
            ->get('/api/enterprise', ['per_page' => 10])
            ->assertOk()
            ->assertJsonPath('last_page', 2);
    }

    public function testGetUnauthenticated()
    {
        $this->withHeaders(['Accept' => 'application/json'])
            ->get("api/enterprise/1")
            ->assertUnauthorized();
    }

    public function testGetUnauthorized()
    {
        $enterprise = Enterprise::factory()->create();
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $this->withHeaders(['Accept' => 'application/json'])
            ->get("api/enterprise/$enterprise->id")
            ->assertForbidden();
    }

    public function testGetSuccess()
    {
        $user = User::factory()->create(['enterprise_id' => null, 'store_id' => null]);
        $enterprise = Enterprise::factory()->create();
        Sanctum::actingAs($user);
        $this->withHeaders(['Accept' => 'application/json'])
            ->get("api/enterprise/{$enterprise->id}")
            ->assertJsonPath('id', $enterprise->id)
            ->assertOk();
    }

    public function testCreateUnauthenticated() {
        $this->withHeaders(['Accept' => 'application/json'])
            ->post("api/enterprise/")
            ->assertUnauthorized();
    }

    public function testCreateSuccess()
    {
        $user = User::factory()->create(['enterprise_id' => null, 'store_id' => null]);
        $enterprise = Enterprise::factory()->make();
        Sanctum::actingAs($user);
        $payload = $enterprise->only($enterprise->getFillable());
        $this->withHeaders(['Accept' => 'application/json'])
            ->post("api/enterprise/", $payload)
            ->assertCreated();
        $this->assertDatabaseHas($enterprise->getTable(), $payload);
    }

    public function testUpdateUnauthenticated()
    {
        $this->withHeaders(['Accept' => 'application/json'])
            ->put("api/enterprise/1")
            ->assertUnauthorized();
    }

    public function testUpdateUnauthorized()
    {
        $user = User::factory()->create();
        $enterprise = Enterprise::factory()->create();
        Sanctum::actingAs($user);
        $this->withHeaders(['Accept' => 'application/json'])
            ->put("api/enterprise/$enterprise->id")
            ->assertForbidden();
    }

    public function testUpdateSuccess()
    {
        $user = User::factory()->create(['enterprise_id' => null, 'store_id' => null]);
        Sanctum::actingAs($user);
        $enterprise = Enterprise::factory()->create();
        $name = $this->faker->name;
        $this->withHeaders(['Accept' => 'application/json'])
            ->put("api/enterprise/{$enterprise->id}", ['name' => $name])
            ->assertOk()
            ->assertJsonPath('name', $name);
        $this->assertDatabaseHas($enterprise->getTable(), [
            'id' => $enterprise->id,
            'name' => $name
        ]);
    }

    public function testDeleteUnauthenticated()
    {
        $this->withHeaders(['Accept' => 'application/json'])
            ->delete("api/enterprise/1")
            ->assertUnauthorized();
    }

    public function testDeleteUnauthorized()
    {
        $user = User::factory()->create(['store_id' => null]);
        $enterprise = Enterprise::factory()->create();
        Sanctum::actingAs($user);
        $this->withHeaders(['Accept' => 'application/json'])
            ->delete("api/enterprise/$enterprise->id")
            ->assertForbidden();
    }

    public function testDeleteSuccess()
    {
        $user = User::factory()->create(['enterprise_id' => null, 'store_id' => null]);
        $enterprise = Enterprise::factory()->create();
        Sanctum::actingAs($user);
        $this->withHeaders(['Accept' => 'application/json'])
            ->delete("api/enterprise/{$enterprise->id}")
            ->assertOk();
        $this->assertDeleted($enterprise);
    }

}
