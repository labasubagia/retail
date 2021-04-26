<?php

namespace Tests\Feature;

use App\Models\ProductType;
use App\Models\User;
use App\Models\Enterprise;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProductTypeTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function testPaginateUnauthenticated()
    {
        $this->withHeaders(['Accept' => 'application/json'])
            ->get('api/product-type')
            ->assertUnauthorized();
    }

    public function testPaginateSuccess()
    {
        $count = 20;
        $user = User::factory()
            ->for(Enterprise::factory()->has(ProductType::factory($count), 'productTypes'))
            ->create(['store_id' => null]);
        Sanctum::actingAs($user);

        $this->assertDatabaseCount((new ProductType)->getTable(), $count);
        $response =$this->withHeaders(['Accept' => 'application/json'])
            ->get('/api/product-type', ['per_page' => 10])
            ->assertOk()
            ->assertJsonPath('last_page', 2);
    }

    public function testGetUnauthenticated()
    {
        $this->withHeaders(['Accept' => 'application/json'])
            ->get("api/product-type/1")
            ->assertUnauthorized();
    }

    public function testGetUnauthorized()
    {
        $productType = ProductType::factory()->create();
        $user = User::factory()->create(['store_id' => null]);
        Sanctum::actingAs($user);
        $this->withHeaders(['Accept' => 'application/json'])
            ->get("api/product-type/$productType->id")
            ->assertForbidden();
    }

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

    public function testCreateUnauthenticated() {
        $this->withHeaders(['Accept' => 'application/json'])
            ->post("api/product-type/")
            ->assertUnauthorized();
    }

    public function testCreateSuccess()
    {
        $user = User::factory()->create(['store_id' => null]);
        Sanctum::actingAs($user);
        $productType = ProductType::factory()->make(['enterprise_id' => $user->enterprise_id]);
        $payload = $productType->only($productType->getFillable());
        $this->withHeaders(['Accept' => 'application/json'])
            ->post("api/product-type/", $payload)
            ->assertCreated();
        $this->assertDatabaseHas($productType->getTable(), $payload);
    }

    public function testUpdateUnauthenticated()
    {
        $this->withHeaders(['Accept' => 'application/json'])
            ->put("api/product-type/1")
            ->assertUnauthorized();
    }

    public function testUpdateUnauthorized()
    {
        $user = User::factory()->create(['store_id' => null]);
        $productType = ProductType::factory()->create();
        Sanctum::actingAs($user);
        $this->withHeaders(['Accept' => 'application/json'])
            ->put("api/product-type/$productType->id")
            ->assertForbidden();
    }

    public function testUpdateSuccess()
    {
        $user = User::factory()->create(['store_id' => null]);
        $productType = ProductType::factory()->create(['enterprise_id' => $user->enterprise_id]);
        Sanctum::actingAs($user);
        $name = $this->faker->name;
        $this->withHeaders(['Accept' => 'application/json'])
            ->put("api/product-type/$productType->id", ['name' => $name])
            ->assertOk()
            ->assertJsonPath('name', $name);
        $this->assertDatabaseHas($productType->getTable(), [
            'id' => $productType->id,
            'name' => $name
        ]);
    }

    public function testDeleteUnauthenticated()
    {
        $this->withHeaders(['Accept' => 'application/json'])
            ->delete("api/product-type/1")
            ->assertUnauthorized();
    }

    public function testDeleteUnauthorized()
    {
        $user = User::factory()->create(['store_id' => null]);
        $productType = ProductType::factory()->create();
        Sanctum::actingAs($user);
        $this->withHeaders(['Accept' => 'application/json'])
            ->delete("api/product-type/$productType->id")
            ->assertForbidden();
    }

    public function testDeleteSuccess()
    {
        $user = User::factory()->create(['store_id' => null]);
        $productType = ProductType::factory()->create(['enterprise_id' => $user->enterprise_id]);
        Sanctum::actingAs($user);
        $this->withHeaders(['Accept' => 'application/json'])
            ->delete("api/product-type/$productType->id")
            ->assertOk();
        $this->assertDeleted($productType);
    }

}
