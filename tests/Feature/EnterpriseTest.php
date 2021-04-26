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

    /**
     * @group paginate
     * @group authentication
     */
    public function testPaginateUnauthenticated()
    {
        $this->withHeaders(['Accept' => 'application/json'])
            ->get('api/enterprise')
            ->assertUnauthorized();
    }

    /**
     * @group paginate
     * @group success
     */
    public function testPaginateSuccess()
    {
        $count = 20;
        $user = User::factory()->create(['enterprise_id' => null, 'store_id' => null]);
        Sanctum::actingAs($user);
        Enterprise::factory($count)->create();
        $this->assertDatabaseCount((new Enterprise)->getTable(), $count);
        $response =$this->withHeaders(['Accept' => 'application/json'])
            ->get('/api/enterprise', ['per_page' => 10])
            ->assertOk()
            ->assertJsonPath('last_page', 2);
    }

    /**
     * @group get
     * @group authentication
     */
    public function testGetUnauthenticated()
    {
        $this->withHeaders(['Accept' => 'application/json'])
            ->get("api/enterprise/1")
            ->assertUnauthorized();
    }

    /**
     * @group get
     * @group authorization
     * @group authentication
     */
    public function testGetAuthorization()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $enterprise = Enterprise::factory()->create();
        $this->withHeaders(['Accept' => 'application/json'])
            ->get("api/enterprise/$enterprise->id")
            ->assertForbidden();
    }

    /**
     * @group get
     * @group success
     */
    public function testGetSuccess()
    {
        $user = User::factory()->create(['enterprise_id' => null, 'store_id' => null]);
        Sanctum::actingAs($user);
        $enterprise = Enterprise::factory()->create();
        $this->withHeaders(['Accept' => 'application/json'])
            ->get("api/enterprise/{$enterprise->id}")
            ->assertJsonPath('id', $enterprise->id)
            ->assertOk();
    }

    /**
     * @group create
     * @group authentication
     */
    public function testCreateUnauthenticated() {
        $this->withHeaders(['Accept' => 'application/json'])
            ->post("api/enterprise/")
            ->assertUnauthorized();
    }

    /**
     * @group create
     * @group success
     */
    public function testCreateSuccess()
    {
        $user = User::factory()->create(['enterprise_id' => null, 'store_id' => null]);
        Sanctum::actingAs($user);
        $enterprise = Enterprise::factory()->make();
        $payload = $enterprise->only($enterprise->getFillable());
        $this->withHeaders(['Accept' => 'application/json'])
            ->post("api/enterprise/", $payload)
            ->assertCreated();
        $this->assertDatabaseHas($enterprise->getTable(), $payload);
    }

    /**
     * @group update
     * @group authentication
     */
    public function testUpdateUnauthenticated()
    {
        $this->withHeaders(['Accept' => 'application/json'])
            ->put("api/enterprise/1")
            ->assertUnauthorized();
    }

    /**
     * @group update
     * @group authorization
     * @group authentication
     */
    public function testUpdateAuthorization()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $enterprise = Enterprise::factory()->create();
        $this->withHeaders(['Accept' => 'application/json'])
            ->put("api/enterprise/$enterprise->id")
            ->assertForbidden();
    }

    /**
     * @group update
     * @group success
     */
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

    /**
     * @group delete
     * @group authentication
     */
    public function testDeleteUnauthenticated()
    {
        $this->withHeaders(['Accept' => 'application/json'])
            ->delete("api/enterprise/1")
            ->assertUnauthorized();
    }

    /**
     * @group delete
     * @group authorization
     * @group authentication
     */
    public function testDeleteAuthorization()
    {
        $user = User::factory()->create(['store_id' => null]);
        Sanctum::actingAs($user);
        $enterprise = Enterprise::factory()->create();
        $this->withHeaders(['Accept' => 'application/json'])
            ->delete("api/enterprise/$enterprise->id")
            ->assertForbidden();
    }

    /**
     * @group delete
     * @group success
     */
    public function testDeleteSuccess()
    {
        $user = User::factory()->create(['enterprise_id' => null, 'store_id' => null]);
        Sanctum::actingAs($user);
        $enterprise = Enterprise::factory()->create();
        $this->withHeaders(['Accept' => 'application/json'])
            ->delete("api/enterprise/{$enterprise->id}")
            ->assertOk();
        $this->assertDeleted($enterprise);
    }
}
