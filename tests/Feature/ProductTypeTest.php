<?php

namespace Tests\Feature;

use App\Models\Enterprise;
use App\Models\ProductType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProductTypeTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * @group paginate
     * @group authentication
     */
    public function testPaginateUnauthenticated()
    {
        $this->withHeaders(['Accept' => 'application/json'])
            ->get('api/product-type')
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
            ->for(Enterprise::factory()->has(ProductType::factory($count), 'productTypes'))
            ->create(['store_id' => null]);
        Sanctum::actingAs($user);

        $this->assertDatabaseCount((new ProductType)->getTable(), $count);
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->get('/api/product-type', ['per_page' => 10])
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
            ->get('api/product-type/1')
            ->assertUnauthorized();
    }

    /**
     * @group get
     * @group authorization
     * @group authentication
     */
    public function testGetAuthorization()
    {
        $productType = ProductType::factory()->create();
        $this->assertDatabaseHas(
            $productType->getTable(),
            $productType->only($productType->getFillable())
        );
        $fn = fn () => $this->withHeaders(['Accept' => 'application/json'])
            ->get("api/product-type/$productType->id");

        // Employee of other enterprise
        Sanctum::actingAs(User::factory()->create());
        $fn()->assertNotFound();

        // Employee of enterprise store
        Sanctum::actingAs(User::factory()->create([
            'enterprise_id' => $productType->enterprise_id,
        ]));
        $fn()->assertOk();

        // Employee of enterprise
        Sanctum::actingAs(User::factory()->create([
            'enterprise_id' => $productType->enterprise_id,
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
            ->for(Enterprise::factory()->has(ProductType::factory(), 'productTypes'))
            ->create(['store_id' => null]);
        Sanctum::actingAs($user);

        $productType = $user->enterprise->productTypes->first();
        $this->withHeaders(['Accept' => 'application/json'])
            ->get("api/product-type/$productType->id")
            ->assertJsonPath('id', $productType->id)
            ->assertOk();
    }

    /**
     * @group create
     * @group authentication
     */
    public function testCreateUnauthenticated()
    {
        $this->withHeaders(['Accept' => 'application/json'])
            ->post('api/product-type/')
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
        $productType = ProductType::factory()->make(['enterprise_id' => $user->enterprise_id]);
        $payload = $productType->only($productType->getFillable());
        $this->withHeaders(['Accept' => 'application/json'])
            ->post('api/product-type/', $payload)
            ->assertCreated();
        $this->assertDatabaseHas($productType->getTable(), $payload);
    }

    /**
     * @group update
     * @group authentication
     */
    public function testUpdateUnauthenticated()
    {
        $this->withHeaders(['Accept' => 'application/json'])
            ->put('api/product-type/1')
            ->assertUnauthorized();
    }

    /**
     * @group update
     * @group authorization
     * @group authentication
     */
    public function testUpdateAuthorization()
    {
        $productType = ProductType::factory()->create();
        $this->assertDatabaseHas(
            $productType->getTable(),
            $productType->only($productType->getFillable())
        );
        $fn = fn () => $this->withHeaders(['Accept' => 'application/json'])
            ->put("api/product-type/$productType->id", ['name' => $this->faker->name]);

        // Employee of other enterprise
        Sanctum::actingAs(User::factory()->create());
        $fn()->assertNotFound();

        // Employee of enterprise store
        Sanctum::actingAs(User::factory()->create([
            'enterprise_id' => $productType->enterprise_id,
        ]));
        $fn()->assertForbidden();

        // Employee of enterprise
        Sanctum::actingAs(User::factory()->create([
            'enterprise_id' => $productType->enterprise_id,
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
        $productType = ProductType::factory()->create(['enterprise_id' => $user->enterprise_id]);
        $name = $this->faker->name;
        $this->withHeaders(['Accept' => 'application/json'])
            ->put("api/product-type/$productType->id", ['name' => $name])
            ->assertOk()
            ->assertJsonPath('name', $name);
        $this->assertDatabaseHas($productType->getTable(), [
            'id' => $productType->id,
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
            ->delete('api/product-type/1')
            ->assertUnauthorized();
    }

    /**
     * @group delete
     * @group authorization
     * @group authentication
     */
    public function testDeleteAuthorization()
    {
        $productType = ProductType::factory()->create();
        $this->assertDatabaseHas(
            $productType->getTable(),
            $productType->only($productType->getFillable())
        );
        $fn = fn () => $this->withHeaders(['Accept' => 'application/json'])
            ->delete("api/product-type/$productType->id");

        // Employee of other enterprise
        Sanctum::actingAs(User::factory()->create());
        $fn()->assertNotFound();

        // Employee of enterprise store
        Sanctum::actingAs(User::factory()->create([
            'enterprise_id' => $productType->enterprise_id,
        ]));
        $fn()->assertForbidden();

        // Employee of enterprise
        Sanctum::actingAs(User::factory()->create([
            'enterprise_id' => $productType->enterprise_id,
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
        $productType = ProductType::factory()->create(['enterprise_id' => $user->enterprise_id]);
        $this->withHeaders(['Accept' => 'application/json'])
            ->delete("api/product-type/$productType->id")
            ->assertOk();
        $this->assertDeleted($productType);
    }
}
