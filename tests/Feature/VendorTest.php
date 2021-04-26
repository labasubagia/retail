<?php

namespace Tests\Feature;

use App\Models\Vendor;
use App\Models\User;
use App\Models\Enterprise;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class VendorTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * @group paginate
     * @group authentication
     */
    public function testPaginateUnauthenticated()
    {
        $this->withHeaders(['Accept' => 'application/json'])
            ->get('api/vendor')
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
            ->for(Enterprise::factory()->has(Vendor::factory($count), 'vendors'))
            ->create(['store_id' => null]);
        Sanctum::actingAs($user);

        $this->assertDatabaseCount('vendors', $count);
        $response =$this->withHeaders(['Accept' => 'application/json'])
            ->get('/api/vendor', ['per_page' => 10])
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
            ->get("api/vendor/1")
            ->assertUnauthorized();
    }


    /**
     * @group get
     * @group authorization
     * @group authentication
     */
    public function testGetAuthorization()
    {
        $vendor = Vendor::factory()->create();
        $this->assertDatabaseHas(
            $vendor->getTable(),
            $vendor->only($vendor->getFillable())
        );
        $fn = fn() => $this
            ->withHeaders(['Accept' => 'application/json'])
            ->get("api/vendor/$vendor->id");

        // Employee of other enterprise
        Sanctum::actingAs(User::factory()->create());
        $fn()->assertNotFound();

        // Employee of enterprise store
        Sanctum::actingAs(User::factory()->create([
            'enterprise_id' => $vendor->enterprise_id,
        ]));
        $fn()->assertOk();

        // Employee of enterprise
        Sanctum::actingAs(User::factory()->create([
            'enterprise_id' => $vendor->enterprise_id,
            'store_id' => null
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
            ->for(Enterprise::factory()->has(Vendor::factory(), 'vendors'))
            ->create(['store_id' => null]);
        Sanctum::actingAs($user);

        $vendor = $user->enterprise->vendors->first();
        $this->withHeaders(['Accept' => 'application/json'])
            ->get("api/vendor/$vendor->id")
            ->assertJsonPath('id', $vendor->id)
            ->assertOk();
    }

    /**
     * @group create
     * @group authentication
     */
    public function testCreateUnauthenticated() {
        $this->withHeaders(['Accept' => 'application/json'])
            ->post("api/vendor/")
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
        $vendor = Vendor::factory()->make(['enterprise_id' => $user->enterprise_id]);
        $payload = $vendor->only($vendor->getFillable());
        $this->withHeaders(['Accept' => 'application/json'])
            ->post("api/vendor/", $payload)
            ->assertCreated();
        $this->assertDatabaseHas($vendor->getTable(), $payload);
    }

    /**
     * @group update
     * @group authentication
     */
    public function testUpdateUnauthenticated()
    {
        $this->withHeaders(['Accept' => 'application/json'])
            ->put("api/vendor/1")
            ->assertUnauthorized();
    }


    /**
     * @group update
     * @group authorization
     * @group authentication
     */
    public function testUpdateAuthorization()
    {
        $vendor = Vendor::factory()->create();
        $this->assertDatabaseHas(
            $vendor->getTable(),
            $vendor->only($vendor->getFillable())
        );
        $fn = fn() => $this
            ->withHeaders(['Accept' => 'application/json'])
            ->put("api/vendor/$vendor->id");

        // Employee of other enterprise
        Sanctum::actingAs(User::factory()->create());
        $fn()->assertNotFound();

        // Employee of enterprise store
        Sanctum::actingAs(User::factory()->create([
            'enterprise_id' => $vendor->enterprise_id,
        ]));
        $fn()->assertForbidden();

        // Employee of enterprise
        Sanctum::actingAs(User::factory()->create([
            'enterprise_id' => $vendor->enterprise_id,
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
        $vendor = Vendor::factory()->create(['enterprise_id' => $user->enterprise_id]);
        $name = $this->faker->name;
        $this->withHeaders(['Accept' => 'application/json'])
            ->put("api/vendor/$vendor->id", ['name' => $name])
            ->assertOk()
            ->assertJsonPath('name', $name);
        $this->assertDatabaseHas($vendor->getTable(), [
            'id' => $vendor->id,
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
            ->delete("api/vendor/1")
            ->assertUnauthorized();
    }

    /**
     * @group delete
     * @group authorization
     * @group authentication
     */
    public function testDeleteAuthorization()
    {
        $vendor = Vendor::factory()->create();
        $this->assertDatabaseHas(
            $vendor->getTable(),
            $vendor->only($vendor->getFillable())
        );
        $fn = fn() => $this
            ->withHeaders(['Accept' => 'application/json'])
            ->delete("api/vendor/$vendor->id");

        // Employee of other enterprise
        Sanctum::actingAs(User::factory()->create());
        $fn()->assertNotFound();

        // Employee of enterprise store
        Sanctum::actingAs(User::factory()->create([
            'enterprise_id' => $vendor->enterprise_id,
        ]));
        $fn()->assertForbidden();

        // Employee of enterprise
        Sanctum::actingAs(User::factory()->create([
            'enterprise_id' => $vendor->enterprise_id,
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
        $vendor = Vendor::factory()->create(['enterprise_id' => $user->enterprise_id]);
        $this->withHeaders(['Accept' => 'application/json'])
            ->delete("api/vendor/$vendor->id")
            ->assertOk();
        $this->assertDeleted($vendor);
    }

}
