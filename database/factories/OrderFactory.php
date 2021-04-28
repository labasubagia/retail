<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Enterprise;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Order::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'enterprise_id' => Enterprise::factory(),
            'store_id' => Store::factory(),
            'user_id' => User::factory(),
            'total' => $this->faker->numberBetween(20, 40) * 1000,
        ];
    }
}
