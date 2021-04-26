<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use App\Models\Enterprise;
use App\Models\User;
use App\Models\Brand;
use App\Models\Vendor;
use App\Models\Product;
use App\Models\ProductType;
use App\Models\Store;
use App\Models\StoreStock;
use App\Models\TransactionOrder;
use App\Models\TransactionOrderItem;


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
        Enterprise::factory(2)
            ->create()
            ->each(function($enterprise) {

                $brandIds = Brand::factory(5)
                    ->create(['enterprise_id' => $enterprise->id])
                    ->map(fn($v) => $v->id)
                    ->toArray();

                $vendorIds = Vendor::factory(5)
                    ->create(['enterprise_id' => $enterprise->id])
                    ->map(fn($v) => $v->id)
                    ->toArray();

                $typeIds = ProductType::factory(5)
                    ->create(['enterprise_id' => $enterprise->id])
                    ->map(fn($v) => $v->id)
                    ->toArray();

                $productIds = collect([]);
                for($i=0; $i<10; $i++) {
                    $product = Product::factory()->create([
                        'enterprise_id' => $enterprise->id,
                        'brand_id' => Arr::random($brandIds),
                        'vendor_id' => Arr::random($vendorIds),
                        'product_type_id' => Arr::random($typeIds),
                    ]);
                    $productIds->push($product->id);
                }

                // enterprise employee
                User::factory(2)->create([
                    'enterprise_id' => $enterprise->id,
                    'store_id' => null,
                ]);

                Store::factory(2)
                    ->create(['enterprise_id' => $enterprise->id])
                    ->each(function($store) use ($productIds) {

                        // store employee
                        $userIds = User::factory(2)
                            ->create([
                                'enterprise_id' => $store->enterprise_id,
                                'store_id' => $store->id,
                            ])
                            ->map(fn($v) => $v->id)
                            ->toArray();

                        $productIds->each(function($productId) use ($store) {
                            StoreStock::factory()->create([
                                'enterprise_id' => $store->enterprise_id,
                                'store_id' => $store->id,
                                'product_id' => $productId,
                            ]);
                        });

                        collect($userIds)->each(function ($userId) use ($productIds, $store) {
                            TransactionOrder::factory()
                            ->create([
                                'enterprise_id' => $store->enterprise_id,
                                'store_id' => $store->id,
                                'user_id' => $userId,
                            ])
                            ->each(function ($order) use ($productIds, $store) {

                                $productIds->each(function($productId) use ($order, $store) {
                                    TransactionOrderItem::factory()->create([
                                        'enterprise_id' => $order->enterprise_id,
                                        'store_id' => $order->store_id,
                                        'user_id' => $order->user_id,
                                        'transaction_order_id' => $order->id,
                                        'product_id' => $productId,
                                    ]);
                                });
                            });
                        });
                    });
        });
    }

}
