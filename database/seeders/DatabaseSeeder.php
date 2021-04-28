<?php

namespace Database\Seeders;


use App\Models\Enterprise;
use App\Models\User;
use App\Models\Brand;
use App\Models\Vendor;
use App\Models\Product;
use App\Models\ProductType;
use App\Models\Store;
use App\Models\StoreStock;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Support\Arr;
use Laravel\Sanctum\Sanctum;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->generate();
    }

    public function generate()
    {
        // Admin
        $user = User::factory()->create(['enterprise_id' => null, 'store_id' => null]);
        Sanctum::actingAs($user);

        Enterprise::factory(2)
            ->create()
            ->each(function($enterprise) {
                $payload = ['enterprise_id' => $enterprise->id];
                $brands = Brand::factory(5)->create($payload);
                $vendors = Vendor::factory(5)->create($payload);
                $types = ProductType::factory(5)->create($payload);
                $products = Product::factory(10)
                    ->state(new Sequence(fn() => [
                        'brand_id' => $brands->random()->id,
                        'vendor_id' => $vendors->random()->id,
                        'product_type_id' => $vendors->random()->id,
                    ]))
                    ->create($payload);

                // Enterprise Employees
                $users = User::factory(2)
                    ->create([
                        'enterprise_id' => $enterprise->id,
                        'store_id' => null,
                    ]);
                Sanctum::actingAs($users->random());

                Store::factory(2)
                    ->create(['enterprise_id' => $enterprise->id])
                    ->each(function($store) use ($products) {
                        $payload = [
                            'enterprise_id' => $store->enterprise_id,
                            'store_id' => $store->id,
                        ];

                        // Store Employees
                        $users = User::factory(2)->create($payload);

                        $products->each(fn($product) =>
                            StoreStock::factory()->create(array_merge(
                                $payload, [
                                    'product_id' => $product->id,
                                ]
                            ))
                        );

                        Order::factory(10)
                            ->state(new Sequence(fn() => ['user_id' => $users->random()->id]))
                            ->create($payload)
                            ->each(function ($order) use ($products) {
                                OrderItem::factory()
                                    ->state(new Sequence(fn() => [
                                        'product_id' => $products->random()->id
                                    ]))
                                    ->create([
                                        'enterprise_id' => $order->enterprise_id,
                                        'store_id' => $order->store_id,
                                        'user_id' => $order->user_id,
                                        'order_id' => $order->id,
                                    ]);
                            });
                    });
            });
    }
}
