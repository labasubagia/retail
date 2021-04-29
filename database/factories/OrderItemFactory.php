<?php

namespace Database\Factories;

use App\Models\Enterprise;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderItemFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = OrderItem::class;

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
            'user_id' => User::factory(),
            'order_id' => Order::factory(),
            'amount' => $this->faker->numberBetween(1, 5),
            'subtotal' => fn (array $attributes) => Product::find($attributes['product_id'])->price * $attributes['amount'],
        ];
    }
}
