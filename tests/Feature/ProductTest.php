<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductType;
use App\Models\Brand;
use App\Models\Vendor;
use App\Models\User;
use App\Models\Enterprise;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function testPaginateUnauthenticated()
    {
        $this->withHeaders(['Accept' => 'application/json'])
            ->get('api/product')
            ->assertUnauthorized();
    }

    public function testPaginateSuccess()
    {
        $count = 20;
        $user = User::factory()
            ->for(Enterprise::factory()->has(Product::factory($count), 'products'))
            ->create(['store_id' => null]);
        Sanctum::actingAs($user);

        $this->assertDatabaseCount((new Product)->getTable(), $count);
        $response =$this->withHeaders(['Accept' => 'application/json'])
            ->get('/api/product', ['per_page' => 10])
            ->assertOk()
            ->assertJsonPath('last_page', 2);
    }

    public function testGetUnauthenticated()
    {
        $this->withHeaders(['Accept' => 'application/json'])
            ->get("api/product/1")
            ->assertUnauthorized();
    }

    public function testGetUnauthorized()
    {
        $product = Product::factory()->create();
        $user = User::factory()->create(['store_id' => null]);
        Sanctum::actingAs($user);
        $this->withHeaders(['Accept' => 'application/json'])
            ->get("api/product/$product->id")
            ->assertForbidden();
    }

    public function testGetSuccess()
    {
        $user = User::factory()
            ->for(Enterprise::factory()->has(Product::factory(), 'products'))
            ->create(['store_id' => null]);
        Sanctum::actingAs($user);

        $product = $user->enterprise->Products->first();
        $this->withHeaders(['Accept' => 'application/json'])
            ->get("api/product/$product->id")
            ->assertJsonPath('id', $product->id)
            ->assertOk();
    }

    public function testCreateUnauthenticated() {
        $this->withHeaders(['Accept' => 'application/json'])
            ->post("api/product/")
            ->assertUnauthorized();
    }

    public function testCreateSuccess()
    {
        $user = User::factory()->create(['store_id' => null]);
        Sanctum::actingAs($user);
        $product = Product::factory()->make([
            'enterprise_id' => $user->enterprise_id,
        ]);
        $payload = $product->only($product->getFillable());
        $this->withHeaders(['Accept' => 'application/json'])
            ->post("api/product/", $payload)
            ->assertCreated();
        $this->assertDatabaseHas($product->getTable(), $payload);
    }

    public function testUpdateUnauthenticated()
    {
        $this->withHeaders(['Accept' => 'application/json'])
            ->put("api/product/1", ['name' => 'random'])
            ->assertUnauthorized();
    }

    public function testUpdateUnauthorized()
    {
        $user = User::factory()->create(['store_id' => null]);
        $product = Product::factory()->create();
        Sanctum::actingAs($user);
        $this->withHeaders(['Accept' => 'application/json'])
            ->put("api/product/$product->id", ['name' => $this->faker->name])
            ->assertForbidden();
    }

    public function testUpdateSuccess()
    {
        $user = User::factory()->create(['store_id' => null]);
        $product = Product::factory()->create(['enterprise_id' => $user->enterprise_id]);
        Sanctum::actingAs($user);
        $name = $this->faker->name;
        $this->withHeaders(['Accept' => 'application/json'])
            ->put("api/product/$product->id", ['name' => $name])
            ->assertOk()
            ->assertJsonPath('name', $name);
        $this->assertDatabaseHas($product->getTable(), [
            'id' => $product->id,
            'name' => $name
        ]);
    }

    public function testDeleteUnauthenticated()
    {
        $this->withHeaders(['Accept' => 'application/json'])
            ->delete("api/product/1")
            ->assertUnauthorized();
    }

    public function testDeleteUnauthorized()
    {
        $user = User::factory()->create(['store_id' => null]);
        $product = Product::factory()->create();
        Sanctum::actingAs($user);
        $this->withHeaders(['Accept' => 'application/json'])
            ->delete("api/product/$product->id")
            ->assertForbidden();
    }

    public function testDeleteSuccess()
    {
        $user = User::factory()->create(['store_id' => null]);
        $product = Product::factory()->create(['enterprise_id' => $user->enterprise_id]);
        Sanctum::actingAs($user);
        $this->withHeaders(['Accept' => 'application/json'])
            ->delete("api/product/$product->id")
            ->assertOk();
        $this->assertDeleted($product);
    }
}
