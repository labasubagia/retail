<?php

namespace Tests\Feature;

use App\Models\StoreStock;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class StoreStockTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * @group upsert
     * @group authentication
     */
    public function testUpsertUnauthenticated()
    {
        $this->withHeaders(['Accept' => 'application/json'])
            ->post('api/store-stock/')
            ->assertUnauthorized();
    }

    /**
     * @group upsert
     * @group authorization
     * @group authentication
     */
    public function testUpsertAuthorization()
    {
        $storeStock = StoreStock::factory()->create();
        $this->assertDatabaseHas(
            $storeStock->getTable(),
            $storeStock->only($storeStock->getFillable())
        );
        $payload = [
            'product_id' => $storeStock->product_id,
            'stock' => $this->faker->numberBetween(10, 50),
        ];
        $fnCreate = fn () => $this
            ->withHeaders(['Accept' => 'application/json'])
            ->post('api/store-stock', $payload);
        $fnUpdate = fn () => $this
            ->withHeaders(['Accept' => 'application/json'])
            ->post("api/store-stock/$storeStock->id", $payload);

        // Employee of other store
        Sanctum::actingAs(User::factory()->create());
        $fnCreate()->assertOk();
        $fnUpdate()->assertNotFound();

        // Employee of enterprise
        Sanctum::actingAs(User::factory()->create([
            'enterprise_id' => $storeStock->enterprise_id,
            'store_id' => null,
        ]));
        $fnCreate()->assertForbidden();
        $fnUpdate()->assertNotFound();

        // Employee of enterprise store
        Sanctum::actingAs(User::factory()->create([
            'enterprise_id' => $storeStock->enterprise_id,
            'store_id' => $storeStock->store_id,
        ]));
        $fnCreate()->assertOk();
        $fnUpdate()->assertOk();
    }

    /**
     * @group upsert
     * @group success
     */
    public function testUpsertSuccessAdd()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $storeStock = StoreStock::factory()->make([
            'enterprise_id' => $user->enterprise_id,
            'store_id' => $user->store_id,
        ]);
        $payload = [
            'product_id' => $storeStock->product_id,
            'stock' => $this->faker->numberBetween(10, 100),
        ];
        $this->assertDatabaseCount($storeStock->getTable(), 0);
        $this->withHeaders(['Accept' => 'application/json'])
            ->post('api/store-stock', $payload)
            ->assertOk()
            ->assertJsonFragment($payload);
        $this->assertDatabaseHas($storeStock->getTable(), $payload);
        $this->assertDatabaseCount($storeStock->getTable(), 1);
    }

    /**
     * @group upsert
     * @group success
     */
    public function testUpsertSuccessModify()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $storeStock = StoreStock::factory()->create([
            'enterprise_id' => $user->enterprise_id,
            'store_id' => $user->store_id,
        ]);
        $payload = [
            'product_id' => $storeStock->product_id,
            'stock' => $this->faker->numberBetween(10, 100),
        ];
        $this->assertDatabaseCount($storeStock->getTable(), 1);
        $this->withHeaders(['Accept' => 'application/json'])
            ->post('api/store-stock', $payload)
            ->assertOk()
            ->assertJsonFragment($payload);
        $this->assertDatabaseHas($storeStock->getTable(), $payload);
        $this->assertDatabaseCount($storeStock->getTable(), 1);
    }

    /**
     * @group upsert
     * @group success
     */
    public function testUpsertSuccessUpdate()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $storeStock = StoreStock::factory()->create([
            'enterprise_id' => $user->enterprise_id,
            'store_id' => $user->store_id,
        ]);
        $payload = [
            'product_id' => $storeStock->product_id,
            'stock' => $this->faker->numberBetween(10, 100),
        ];
        $this->assertDatabaseCount($storeStock->getTable(), 1);
        $this->withHeaders(['Accept' => 'application/json'])
            ->post("api/store-stock/{$storeStock->id}", $payload)
            ->assertOk()
            ->assertJsonFragment($payload);
        $this->assertDatabaseHas($storeStock->getTable(), $payload);
        $this->assertDatabaseCount($storeStock->getTable(), 1);
    }
}
