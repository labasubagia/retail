<?php

namespace Tests\Feature;

use App\Models\TransactionOrder;
use App\Models\TransactionOrderItem;
use App\Models\User;
use App\Models\Enterprise;
use App\Models\Product;
use App\Models\StoreStock;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TransactionOrderTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function testPaginateUnauthenticated()
    {
        $this->withHeaders(['Accept' => 'application/json'])
            ->get('api/order')
            ->assertUnauthorized();
    }

    public function testPaginateSuccess()
    {
        $count = 20;
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $storePayload = $user->only('enterprise_id', 'store_id');
        TransactionOrder::factory($count)->create(
            array_merge($storePayload, ['user_id' => $user->id])
        );

        $this->assertDatabaseCount((new TransactionOrder)->getTable(), $count);
        $this->withHeaders(['Accept' => 'application/json'])
            ->get('/api/order', ['per_page' => 10])
            ->assertOk()
            ->assertJsonPath('last_page', 2);
    }

    public function testGetUnauthenticated()
    {
        $this->withHeaders(['Accept' => 'application/json'])
            ->get("api/order/1")
            ->assertUnauthorized();
    }

    public function testGetUnauthorized()
    {
        $order = TransactionOrder::factory()->create();
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $this->withHeaders(['Accept' => 'application/json'])
            ->get("api/order/$order->id")
            ->assertForbidden();
    }

    public function testGetSuccess()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $storePayload = $user->only('enterprise_id', 'store_id');
        $order = TransactionOrder::factory()->create(
            array_merge($storePayload, ['user_id' => $user->id])
        );
        $this->withHeaders(['Accept' => 'application/json'])
            ->get("api/order/$order->id")
            ->assertJsonPath('id', $order->id)
            ->assertOk();
    }

    public function testCreateUnauthenticated() {
        $this->withHeaders(['Accept' => 'application/json'])
            ->post("api/order/")
            ->assertUnauthorized();
    }

    public function testCreateUnauthorized() {
        // Not Employee
        Sanctum::actingAs(User::factory()->create(['enterprise_id' => null,'store_id' => null]));
        $this->withHeaders(['Accept' => 'application/json'])
            ->post("api/order/")
            ->assertForbidden();

        // Enterprise Employee
        Sanctum::actingAs(User::factory()->create(['store_id' => null]));
        $this->withHeaders(['Accept' => 'application/json'])
            ->post("api/order/")
            ->assertForbidden();
    }

    public function testCreateSuccess()
    {
        $user = User::factory()->create();
        $enterprisePayload = $user->only('enterprise_id');
        $storePayload = $user->only('enterprise_id', 'store_id');
        $employeePayload = array_merge($storePayload, ['user_id' => $user->id]);
        Sanctum::actingAs($user);

        // Make data
        $stock = $this->faker->numberBetween(10, 50);
        $buy = $this->faker->numberBetween(1, 5);
        $stocks = collect([]);
        $count = 2;
        $product = Product::factory($count)
            ->create($enterprisePayload)
            ->each(function($p) use ($storePayload, $stock, &$stocks) {
                $stocks->push(StoreStock::factory()
                    ->create(
                        array_merge($storePayload, ['product_id' => $p->id, 'stock' => $stock ])
                    )
                );
            });

        // Send Request
        $payload = $product->map(fn($p) => ['product_id' => $p->id, 'amount' => $buy])->toArray();
        $response = $this->withHeaders(['Accept' => 'application/json'])->post("api/order/", $payload);
        $response->assertCreated();
        $result = collect(json_decode($response->getContent()))->except('trace');

        // Check Stock
        $stocks = StoreStock::whereIn('id', $stocks->pluck('id'))->get();
        $isQuantityCorrect = $stocks->every(fn($s) => $s->stock == $stock - $buy);
        $this->assertTrue($isQuantityCorrect);
        $this->assertDatabaseCount((new StoreStock)->getTable(), $count);

        // Check Database
        $this->assertDatabaseHas((new TransactionOrder)->getTable(), [
            'id' => $result->get('id'),
            'total' => $result->get('total')
        ]);
        $this->assertDatabaseCount((new TransactionOrderItem)->getTable(), count($stocks));
    }
}
