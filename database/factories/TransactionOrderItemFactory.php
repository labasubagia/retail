<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\TransactionOrderItem;
use App\Models\TransactionOrder;
use App\Models\Enterprise;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;

class TransactionOrderItemFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TransactionOrderItem::class;

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
            'transaction_order_id' => TransactionOrder::factory(),
            'amount' => $this->faker->numberBetween(1, 5),
            'subtotal' => function (array $attributes) {
                return Product::find($attributes['product_id'])->price * $attributes['amount'];
            }
        ];
    }
}
