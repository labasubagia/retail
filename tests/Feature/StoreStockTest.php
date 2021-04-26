<?php

namespace Tests\Feature;

use App\Models\StoreStock;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class StoreStockTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function testUpsertUnauthenticated()
    {
        $this->withHeaders(['Accept' => 'application/json'])
            ->post("api/store-stock/")
            ->assertUnauthorized();
    }

    public function testUpsertUnauthorized()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $storeStock = StoreStock::factory()->create();
        $this->withHeaders(['Accept' => 'application/json'])
            ->post("api/store-stock/{$storeStock->id}")
            ->assertForbidden();
    }

    public function testUpsertSuccessAddData()
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
            ->post("api/store-stock", $payload)
            ->assertOk()
            ->assertJsonFragment($payload);
        $this->assertDatabaseHas($storeStock->getTable(), $payload);
        $this->assertDatabaseCount($storeStock->getTable(), 1);
    }

    public function testUpsertEditData()
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
            ->post("api/store-stock", $payload)
            ->assertOk()
            ->assertJsonFragment($payload);
        $this->assertDatabaseHas($storeStock->getTable(), $payload);
        $this->assertDatabaseCount($storeStock->getTable(), 1);
    }

    public function testUpsertSuccessUpdateData()
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
