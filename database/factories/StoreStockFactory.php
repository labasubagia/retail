<?php

namespace Database\Factories;

use App\Models\StoreStock;
use App\Models\Store;
use App\Models\Product;
use App\Models\Enterprise;
use Illuminate\Database\Eloquent\Factories\Factory;

class StoreStockFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = StoreStock::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'enterprise_id' => Enterprise::factory(),
            'product_id' => Product::factory(),
            'store_id' => Store::factory(),
            'stock' => $this->faker->numberBetween(50, 100),
        ];
    }
}
