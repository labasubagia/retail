<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Enterprise;
use App\Models\Brand;
use App\Models\Vendor;
use App\Models\ProductType;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'enterprise_id' => Enterprise::factory(),
            'brand_id' => Brand::factory(),
            'vendor_id' => Vendor::factory(),
            'product_type_id' => ProductType::factory(),
            'name' => $this->faker->word,
            'price' => $this->faker->numberBetween(1, 30) * 1000,
        ];
    }
}
