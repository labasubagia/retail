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

    public function testPaginateUnauthenticated()
    {
        $this->withHeaders(['Accept' => 'application/json'])
            ->get('api/vendor')
            ->assertUnauthorized();
    }

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

    public function testGetUnauthenticated()
    {
        $this->withHeaders(['Accept' => 'application/json'])
            ->get("api/vendor/1")
            ->assertUnauthorized();
    }

    public function testGetUnauthorized()
    {
        $vendor = Vendor::factory()->create();
        $user = User::factory()->create(['store_id' => null]);
        Sanctum::actingAs($user);
        $this->withHeaders(['Accept' => 'application/json'])
            ->get("api/vendor/$vendor->id")
            ->assertForbidden();
    }

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

    public function testCreateUnauthenticated() {
        $this->withHeaders(['Accept' => 'application/json'])
            ->post("api/vendor/")
            ->assertUnauthorized();
    }

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

    public function testUpdateUnauthenticated()
    {
        $this->withHeaders(['Accept' => 'application/json'])
            ->put("api/vendor/1")
            ->assertUnauthorized();
    }

    public function testUpdateUnauthorized()
    {
        $user = User::factory()->create(['store_id' => null]);
        $vendor = Vendor::factory()->create();
        Sanctum::actingAs($user);
        $this->withHeaders(['Accept' => 'application/json'])
            ->put("api/vendor/$vendor->id")
            ->assertForbidden();
    }

    public function testUpdateSuccess()
    {
        $user = User::factory()->create(['store_id' => null]);
        $vendor = Vendor::factory()->create(['enterprise_id' => $user->enterprise_id]);
        Sanctum::actingAs($user);
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

    public function testDeleteUnauthenticated()
    {
        $this->withHeaders(['Accept' => 'application/json'])
            ->delete("api/vendor/1")
            ->assertUnauthorized();
    }

    public function testDeleteUnauthorized()
    {
        $user = User::factory()->create(['store_id' => null]);
        $vendor = Vendor::factory()->create();
        Sanctum::actingAs($user);
        $this->withHeaders(['Accept' => 'application/json'])
            ->delete("api/vendor/$vendor->id")
            ->assertForbidden();
    }

    public function testDeleteSuccess()
    {
        $user = User::factory()->create(['store_id' => null]);
        $vendor = Vendor::factory()->create(['enterprise_id' => $user->enterprise_id]);
        Sanctum::actingAs($user);
        $this->withHeaders(['Accept' => 'application/json'])
            ->delete("api/vendor/$vendor->id")
            ->assertOk();
        $this->assertDeleted($vendor);
    }

}
