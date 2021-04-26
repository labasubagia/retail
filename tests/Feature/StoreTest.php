<?php

namespace Tests\Feature;

use App\Models\Store;
use App\Models\User;
use App\Models\Enterprise;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class StoreTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function testPaginateUnauthenticated()
    {
        $this->withHeaders(['Accept' => 'application/json'])
            ->get('api/store')
            ->assertUnauthorized();
    }

    public function testPaginateSuccess()
    {
        $count = 20;
        $user = User::factory()
            ->for(Enterprise::factory()->has(Store::factory($count), 'stores'))
            ->create(['store_id' => null]);
        Sanctum::actingAs($user);

        $this->assertDatabaseCount('stores', $count);
        $response =$this->withHeaders(['Accept' => 'application/json'])
            ->get('/api/store', ['per_page' => 10])
            ->assertOk()
            ->assertJsonPath('last_page', 2);
    }

    public function testGetUnauthenticated()
    {
        $this->withHeaders(['Accept' => 'application/json'])
            ->get("api/store/1")
            ->assertUnauthorized();
    }

    public function testGetUnauthorized()
    {
        $store = Store::factory()->create();
        $user = User::factory()->create(['store_id' => null]);
        Sanctum::actingAs($user);
        $this->withHeaders(['Accept' => 'application/json'])
            ->get("api/store/$store->id")
            ->assertForbidden();
    }

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

    public function testCreateUnauthenticated() {
        $this->withHeaders(['Accept' => 'application/json'])
            ->post("api/store/")
            ->assertUnauthorized();
    }

    public function testCreateSuccess()
    {
        $user = User::factory()->create(['store_id' => null]);
        Sanctum::actingAs($user);
        $store = Store::factory()->make(['enterprise_id' => $user->enterprise_id]);
        $payload = $store->only($store->getFillable());
        $this->withHeaders(['Accept' => 'application/json'])
            ->post("api/store/", $payload)
            ->assertCreated();
        $this->assertDatabaseHas($store->getTable(), $payload);
    }

    public function testUpdateUnauthenticated()
    {
        $this->withHeaders(['Accept' => 'application/json'])
            ->put("api/store/1")
            ->assertUnauthorized();
    }

    public function testUpdateUnauthorized()
    {
        $user = User::factory()->create(['store_id' => null]);
        $store = Store::factory()->create();
        Sanctum::actingAs($user);
        $this->withHeaders(['Accept' => 'application/json'])
            ->put("api/store/$store->id")
            ->assertForbidden();
    }

    public function testUpdateSuccess()
    {
        $user = User::factory()->create(['store_id' => null]);
        $store = Store::factory()->create(['enterprise_id' => $user->enterprise_id]);
        Sanctum::actingAs($user);
        $name = $this->faker->name;
        $this->withHeaders(['Accept' => 'application/json'])
            ->put("api/store/$store->id", ['name' => $name])
            ->assertOk()
            ->assertJsonPath('name', $name);
        $this->assertDatabaseHas($store->getTable(), [
            'id' => $store->id,
            'name' => $name
        ]);
    }

    public function testDeleteUnauthenticated()
    {
        $this->withHeaders(['Accept' => 'application/json'])
            ->delete("api/store/1")
            ->assertUnauthorized();
    }

    public function testDeleteUnauthorized()
    {
        $user = User::factory()->create(['store_id' => null]);
        $store = Store::factory()->create();
        Sanctum::actingAs($user);
        $this->withHeaders(['Accept' => 'application/json'])
            ->delete("api/store/$store->id")
            ->assertForbidden();
    }

    public function testDeleteSuccess()
    {
        $user = User::factory()->create(['store_id' => null]);
        $store = Store::factory()->create(['enterprise_id' => $user->enterprise_id]);
        Sanctum::actingAs($user);
        $this->withHeaders(['Accept' => 'application/json'])
            ->delete("api/store/$store->id")
            ->assertOk();
        $this->assertDeleted($store);
    }

}
