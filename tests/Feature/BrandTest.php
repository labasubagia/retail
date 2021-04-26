<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\User;
use App\Models\Enterprise;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class BrandTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * @group paginate
     * @group authentication
     */
    public function testPaginateUnauthenticated()
    {
        $this->withHeaders(['Accept' => 'application/json'])
            ->get('api/brand')
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
            ->for(Enterprise::factory()->has(Brand::factory($count), 'brands'))
            ->create(['store_id' => null]);
        Sanctum::actingAs($user);

        $this->assertDatabaseCount((new Brand)->getTable(), $count);
        $this->withHeaders(['Accept' => 'application/json'])
            ->get('/api/brand', ['per_page' => 10])
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
            ->get("api/brand/1")
            ->assertUnauthorized();
    }

    /**
     * @group get
     * @group authorization
     * @group authentication
     */
    public function testGetUnauthorized()
    {
        $brand = Brand::factory()->create();
        $this->assertDatabaseHas(
            $brand->getTable(),
            $brand->only($brand->getFillable())
        );
        $fn = fn() => $this
            ->withHeaders(['Accept' => 'application/json'])
            ->get("api/brand/$brand->id");

        // Employee of other enterprise
        Sanctum::actingAs(User::factory()->create());
        $fn()->assertNotFound();

        // Employee of enterprise
        Sanctum::actingAs(User::factory()->create([
            'enterprise_id' => $brand->enterprise_id
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
            ->for(Enterprise::factory()->has(Brand::factory(), 'brands'))
            ->create(['store_id' => null]);
        Sanctum::actingAs($user);

        $brand = $user->enterprise->brands->first();
        $this->withHeaders(['Accept' => 'application/json'])
            ->get("api/brand/$brand->id")
            ->assertJsonPath('id', $brand->id)
            ->assertOk();
    }

    /**
     * @group create
     * @group authentication
     */
    public function testCreateUnauthenticated() {
        $this->withHeaders(['Accept' => 'application/json'])
            ->post("api/brand/")
            ->assertUnauthorized();
    }

    /**
     * @group create
     * @group success
     */
    public function testCreateSuccess()
    {
        $user = User::factory()->create(['store_id' => null]);
        $brand = Brand::factory()->make();
        $payload = $brand->only('name');
        Sanctum::actingAs($user);
        $this->withHeaders(['Accept' => 'application/json'])
            ->post("api/brand/", $payload)
            ->assertCreated();
        $this->assertDatabaseHas($brand->getTable(), $payload);
    }

    /**
     * @group update
     * @group authentication
     */
    public function testUpdateUnauthenticated()
    {
        $this->withHeaders(['Accept' => 'application/json'])
            ->put("api/brand/1", ['name' => $this->faker->name])
            ->assertUnauthorized();
    }

    /**
     * @update
     * @group authorization
     * @group authentication
     */
    public function testUpdateAuthorization()
    {
        $brand = Brand::factory()->create();
        $this->assertDatabaseHas(
            $brand->getTable(),
            $brand->only($brand->getFillable())
        );
        $fn = fn() => $this
            ->withHeaders(['Accept' => 'application/json'])
            ->put("api/brand/$brand->id", ['name' => $this->faker->name]);

        // Employee of other enterprise
        Sanctum::actingAs(User::factory()->create());
        $fn()->assertNotFound();

        // Employee of enterprise store
        Sanctum::actingAs(User::factory()->create([
            'enterprise_id' => $brand->enterprise_id
        ]));
        $fn()->assertForbidden();

        // Employee of enterprise
        Sanctum::actingAs(User::factory()->create([
            'enterprise_id' => $brand->enterprise_id,
            'store_id' => null,
        ]));
        $fn()->assertOk();
    }

    /**
     * @group delete
     * @group success
     */
    public function testUpdateSuccess()
    {
        $user = User::factory()->create(['store_id' => null]);
        $brand = Brand::factory()->create(['enterprise_id' => $user->enterprise_id]);
        $name = $this->faker->name;
        Sanctum::actingAs($user);
        $this->withHeaders(['Accept' => 'application/json'])
            ->put("api/brand/$brand->id", ['name' => $name])
            ->assertOk()
            ->assertJsonPath('name', $name);
        $this->assertDatabaseHas($brand->getTable(), [
            'id' => $brand->id,
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
            ->delete("api/brand/1")
            ->assertUnauthorized();
    }

    /**
     * @group delete
     * @group authentication
     * @group authorization
     */
    public function testDeleteAuthorization()
    {
        $brand = Brand::factory()->create();
        $this->assertDatabaseHas(
            $brand->getTable(),
            $brand->only($brand->getFillable())
        );
        $fn = fn() => $this
            ->withHeaders(['Accept' => 'application/json'])
            ->delete("api/brand/$brand->id");

        // Employee of other enterprise
        Sanctum::actingAs(User::factory()->create());
        $fn()->assertNotFound();

        // Employee of enterprise store
        Sanctum::actingAs(User::factory()->create([
            'enterprise_id' => $brand->enterprise_id,
        ]));
        $fn()->assertForbidden();

        // Employee of enterprise
        Sanctum::actingAs(User::factory()->create([
            'enterprise_id' => $brand->enterprise_id,
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
        $brand = Brand::factory()->create(['enterprise_id' => $user->enterprise_id]);
        Sanctum::actingAs($user);
        $this->withHeaders(['Accept' => 'application/json'])
            ->delete("api/brand/$brand->id")
            ->assertOk();
        $this->assertDeleted($brand);
    }

}
