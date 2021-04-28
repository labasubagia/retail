<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\OrderItem;
use App\Models\Order;
use App\Models\Enterprise;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;

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
            'subtotal' => function (array $attributes) {
                return Product::find($attributes['product_id'])->price * $attributes['amount'];
            }
        ];
    }
}
