<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $customer = DB::table('users')->pluck('id');
        $product = DB::table('products')->pluck('id');
        return [
            'shipping_deliverie_id' => $this->faker->randomElement($customer),
            'customer_id' => $this->faker->randomElement($customer),
            'order_id' => randomNumber(),
            'qty' => $this->faker->numberBetween($min = 1, $max = 10),
            'shipping_charge' => $this->faker->numberBetween($min = 1, $max = 500),
            'discount' => $this->faker->numberBetween($min = 1, $max = 500),
            'amount' => $this->faker->numberBetween($min = 1, $max = 500),
            'order_type' => $this->faker->numberBetween($min = 101, $max = 102),
            'payment_type' => $this->faker->numberBetween($min = 1, $max = 2),
            'payment_status' => $this->faker->numberBetween($min = 1, $max = 2),
            'status' => $this->faker->numberBetween($min = 1, $max = 6),
        ];
    }
}
