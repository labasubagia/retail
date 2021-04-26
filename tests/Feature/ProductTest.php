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

    /**
     * @group paginate
     * @group authentication
     */
    public function testPaginateUnauthenticated()
    {
        $this->withHeaders(['Accept' => 'application/json'])
            ->get('api/product')
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
            ->for(Enterprise::factory()->has(Product::factory($count), 'products'))
            ->create(['store_id' => null]);
        Sanctum::actingAs($user);
        $this->assertDatabaseCount((new Product)->getTable(), $count);
        $response = $this
            ->withHeaders(['Accept' => 'application/json'])
            ->get('/api/product', ['per_page' => 10]);
        $response
            ->assertOk()
            ->assertJsonPath('last_page', 2);
        $result = collect(json_decode($response->getContent()))->except('trace');
    }

    /**
     * @group get
     * @group authentication
     */
    public function testGetUnauthenticated()
    {
        $this->withHeaders(['Accept' => 'application/json'])
            ->get("api/product/1")
            ->assertUnauthorized();
    }

    /**
     * @group get
     * @group authorization
     * @group authentication
     */
    public function testGetAuthorization()
    {
        $product = Product::factory()->create();
        $this->assertDatabaseHas(
            $product->getTable(),
            $product->only($product->getFillable())
        );
        $fn = fn() => $this
            ->withHeaders(['Accept' => 'application/json'])
            ->get("api/product/$product->id");

        // Employee of this enterprise
        Sanctum::actingAs(User::factory()->create([
            'enterprise_id' => $product->enterprise_id,
            'store_id' => null
        ]));
        $fn()->assertOk();

        // Employee of other enterprise
        Sanctum::actingAs(User::factory()->create());
        $fn()->assertNotFound();
    }

    /**
     * @group get
     * @group success
     */
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

    /**
     * @group create
     * @group authentication
     */
    public function testCreateUnauthenticated() {
        $this->withHeaders(['Accept' => 'application/json'])
            ->post("api/product/")
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
        $product = Product::factory()->make([
            'enterprise_id' => $user->enterprise_id,
        ]);
        $payload = $product->only($product->getFillable());
        $this->withHeaders(['Accept' => 'application/json'])
            ->post("api/product/", $payload)
            ->assertCreated();
        $this->assertDatabaseHas($product->getTable(), $payload);
    }

    /**
     * @group update
     * @group authentication
     */
    public function testUpdateUnauthenticated()
    {
        $this->withHeaders(['Accept' => 'application/json'])
            ->put("api/product/1", ['name' => 'random'])
            ->assertUnauthorized();
    }

    /**
     * @group update
     * @group authorization
     * @group authentication
     */
    public function testUpdateAuthorization()
    {
        $product = Product::factory()->create();
        $this->assertDatabaseHas(
            $product->getTable(),
            $product->only($product->getFillable())
        );
        $payload = ['name' => $this->faker->name];
        $fn = fn() => $this
            ->withHeaders(['Accept' => 'application/json'])
            ->put("api/product/$product->id", $payload);

        // Employee of other enterprise
        Sanctum::actingAs(User::factory()->create());
        $fn()->assertNotFound();

        // Employee of enterprise store
        Sanctum::actingAs(User::factory()->create([
            'enterprise_id' => $product->enterprise_id,
        ]));
        $fn()->assertForbidden();

        // Employee of enterprise
        Sanctum::actingAs(User::factory()->create([
            'enterprise_id' => $product->enterprise_id,
            'store_id' => null
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
        $product = Product::factory()->create(['enterprise_id' => $user->enterprise_id]);
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

    /**
     * @group delete
     * @group authentication
     */
    public function testDeleteUnauthenticated()
    {
        $this->withHeaders(['Accept' => 'application/json'])
            ->delete("api/product/1")
            ->assertUnauthorized();
    }

    /**
     * @group delete
     * @group authorization
     * @group authentication
     */
    public function testDeleteAuthorization()
    {
        $product = Product::factory()->create();
        $this->assertDatabaseHas(
            $product->getTable(),
            $product->only($product->getFillable())
        );
        $fn = fn() => $this
            ->withHeaders(['Accept' => 'application/json'])
            ->delete("api/product/$product->id");

        // Employee of other enterprise
        Sanctum::actingAs(User::factory()->create());
        $fn()->assertNotFound();

        // Employee of enterprise store
        Sanctum::actingAs(User::factory()->create([
            'enterprise_id' => $product->enterprise_id,
        ]));
        $fn()->assertForbidden();

        // Employee of enterprise
        Sanctum::actingAs(User::factory()->create([
            'enterprise_id' => $product->enterprise_id,
            'store_id' => null
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
        $product = Product::factory()->create(['enterprise_id' => $user->enterprise_id]);
        $this->withHeaders(['Accept' => 'application/json'])
            ->delete("api/product/$product->id")
            ->assertOk();
        $this->assertDeleted($product);
    }
}
