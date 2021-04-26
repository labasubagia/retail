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

    public function testPaginateUnauthenticated()
    {
        $this->withHeaders(['Accept' => 'application/json'])
            ->get('api/brand')
            ->assertUnauthorized();
    }

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

    public function testGetUnauthenticated()
    {
        $this->withHeaders(['Accept' => 'application/json'])
            ->get("api/brand/1")
            ->assertUnauthorized();
    }

    public function testGetUnauthorized()
    {
        $brand = Brand::factory()->create();
        $user = User::factory()->create(['store_id' => null]);
        Sanctum::actingAs($user);
        $this->withHeaders(['Accept' => 'application/json'])
            ->get("api/brand/$brand->id")
            ->assertForbidden();
    }

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

    public function testCreateUnauthenticated() {
        $this->withHeaders(['Accept' => 'application/json'])
            ->post("api/brand/")
            ->assertUnauthorized();
    }

    public function testCreateSuccess()
    {
        $user = User::factory()->create(['store_id' => null]);
        Sanctum::actingAs($user);
        $brand = Brand::factory()->make();
        $payload = $brand->only('name');
        $this->withHeaders(['Accept' => 'application/json'])
            ->post("api/brand/", $payload)
            ->assertCreated();
        $this->assertDatabaseHas($brand->getTable(), $payload);
    }

    public function testUpdateUnauthenticated()
    {
        $this->withHeaders(['Accept' => 'application/json'])
            ->put("api/brand/1", ['name' => $this->faker->name])
            ->assertUnauthorized();
    }

    public function testUpdateUnauthorized()
    {
        $brand = Brand::factory()->create();
        $user = User::factory()->create(['store_id' => null]);
        Sanctum::actingAs($user);
        $this->withHeaders(['Accept' => 'application/json'])
            ->put("api/brand/$brand->id", ['name' => $this->faker->name])
            ->assertForbidden();
    }

    public function testUpdateSuccess()
    {
        $user = User::factory()->create(['store_id' => null]);
        $brand = Brand::factory()->create(['enterprise_id' => $user->enterprise_id]);
        Sanctum::actingAs($user);
        $name = $this->faker->name;
        $this->withHeaders(['Accept' => 'application/json'])
            ->put("api/brand/$brand->id", ['name' => $name])
            ->assertOk()
            ->assertJsonPath('name', $name);
        $this->assertDatabaseHas($brand->getTable(), [
            'id' => $brand->id,
            'name' => $name
        ]);
    }

    public function testDeleteUnauthenticated()
    {
        $this->withHeaders(['Accept' => 'application/json'])
            ->delete("api/brand/1")
            ->assertUnauthorized();
    }

    public function testDeleteUnauthorized()
    {
        $user = User::factory()->create(['store_id' => null]);
        $brand = Brand::factory()->create();
        Sanctum::actingAs($user);
        $this->withHeaders(['Accept' => 'application/json'])
            ->delete("api/brand/$brand->id")
            ->assertForbidden();
    }

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
