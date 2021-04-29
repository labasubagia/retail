<?php

namespace Tests\Feature;

use App\Models\Enterprise;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class StoreTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * @group paginate
     * @group authentication
     */
    public function testPaginateUnauthenticated()
    {
        $this->withHeaders(['Accept' => 'application/json'])
            ->get('api/store')
            ->assertUnauthorized();
    }

    /**
     * @group paginate
     * @group success
     */
    public function testPaginateSuccess()
    {
        $count = 20;
        $user = User::factory()
            ->for(Enterprise::factory()->has(Store::factory($count), 'stores'))
            ->create(['store_id' => null]);
        Sanctum::actingAs($user);

        $this->assertDatabaseCount('stores', $count);
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->get('/api/store', ['per_page' => 10])
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
            ->get('api/store/1')
            ->assertUnauthorized();
    }

    /**
     * @group get
     * @group authentication
     */
    public function testGetAuthorization()
    {
        $store = Store::factory()->create();
        $this->assertDatabaseHas(
            $store->getTable(),
            $store->only($store->getFillable())
        );
        $fn = fn () => $this
            ->withHeaders(['Accept' => 'application/json'])
            ->get("api/store/$store->id");

        // Employee of other enterprise
        Sanctum::actingAs(User::factory()->create());
        $fn()->assertNotFound();

        // Employee of enterprise store
        Sanctum::actingAs(User::factory()->create([
            'enterprise_id' => $store->enterprise_id,
        ]));
        $fn()->assertForbidden();

        // Employee of enterprise
        Sanctum::actingAs(User::factory()->create([
            'enterprise_id' => $store->enterprise_id,
            'store_id' => null,
        ]));
        $fn()->assertOk();
    }

    /**
     * @group get
     * @group success
     */
    public function testGetSuccess()
    {
        $user = User::factory()
            ->for(Enterprise::factory()->has(Store::factory(), 'stores'))
            ->create(['store_id' => null]);
        Sanctum::actingAs($user);
        $store = $user->enterprise->stores->first();
        $this->withHeaders(['Accept' => 'application/json'])
            ->get("api/store/$store->id")
            ->assertJsonPath('id', $store->id)
            ->assertOk();
    }

    /**
     * @group create
     * @group authentication
     */
    public function testCreateUnauthenticated()
    {
        $this->withHeaders(['Accept' => 'application/json'])
            ->post('api/store/')
            ->assertUnauthorized();
    }

    /**
     * @group create
     * @group success
     */
    public function testCreateSuccess()
    {
        $user = User::factory()->create(['store_id' => null]);
        Sanctum::actingAs($user);
        $store = Store::factory()->make(['enterprise_id' => $user->enterprise_id]);
        $payload = $store->only($store->getFillable());
        $this->withHeaders(['Accept' => 'application/json'])
            ->post('api/store/', $payload)
            ->assertCreated();
        $this->assertDatabaseHas($store->getTable(), $payload);
    }

    /**
     * @group update
     * @group authentication
     */
    public function testUpdateUnauthenticated()
    {
        $this->withHeaders(['Accept' => 'application/json'])
            ->put('api/store/1')
            ->assertUnauthorized();
    }

    /**
     * @group update
     * @group authorization
     * @group authentication
     */
    public function testUpdateAuthorization()
    {
        $store = Store::factory()->create();
        $this->assertDatabaseHas(
            $store->getTable(),
            $store->only($store->getFillable())
        );
        $fn = fn () => $this
            ->withHeaders(['Accept' => 'application/json'])
            ->put("api/store/$store->id");

        // Employee of other enterprise
        Sanctum::actingAs(User::factory()->create());
        $fn()->assertNotFound();

        // Employee of enterprise store
        Sanctum::actingAs(User::factory()->create([
            'enterprise_id' => $store->enterprise_id,
        ]));
        $fn()->assertForbidden();

        // Employee of enterprise
        Sanctum::actingAs(User::factory()->create([
            'enterprise_id' => $store->enterprise_id,
            'store_id' => null,
        ]));
        $fn()->assertOk();
    }

    /**
     * @group update
     * @group success
     */
    public function testUpdateSuccess()
    {
        $user = User::factory()->create(['store_id' => null]);
        Sanctum::actingAs($user);
        $store = Store::factory()->create(['enterprise_id' => $user->enterprise_id]);
        $name = $this->faker->name;
        $this->withHeaders(['Accept' => 'application/json'])
            ->put("api/store/$store->id", ['name' => $name])
            ->assertOk()
            ->assertJsonPath('name', $name);
        $this->assertDatabaseHas($store->getTable(), [
            'id' => $store->id,
            'name' => $name,
        ]);
    }

    /**
     * @group delete
     * @group authentication
     */
    public function testDeleteUnauthenticated()
    {
        $this->withHeaders(['Accept' => 'application/json'])
            ->delete('api/store/1')
            ->assertUnauthorized();
    }

    /**
     * @group update
     * @group authorization
     * @group authentication
     */
    public function testDeleteAuthorization()
    {
        $store = Store::factory()->create();
        $this->assertDatabaseHas(
            $store->getTable(),
            $store->only($store->getFillable())
        );
        $fn = fn () => $this
            ->withHeaders(['Accept' => 'application/json'])
            ->put("api/store/$store->id");

        // Employee of other enterprise
        Sanctum::actingAs(User::factory()->create());
        $fn()->assertNotFound();

        // Employee of enterprise store
        Sanctum::actingAs(User::factory()->create([
            'enterprise_id' => $store->enterprise_id,
        ]));
        $fn()->assertForbidden();

        // Employee of enterprise
        Sanctum::actingAs(User::factory()->create([
            'enterprise_id' => $store->enterprise_id,
            'store_id' => null,
        ]));
        $fn()->assertOk();
    }

    /**
     * @group delete
     * @group success
     */
    public function testDeleteSuccess()
    {
        $user = User::factory()->create(['store_id' => null]);
        Sanctum::actingAs($user);
        $store = Store::factory()->create(['enterprise_id' => $user->enterprise_id]);
        $this->withHeaders(['Accept' => 'application/json'])
            ->delete("api/store/$store->id")
            ->assertOk();
        $this->assertDeleted($store);
    }
}
