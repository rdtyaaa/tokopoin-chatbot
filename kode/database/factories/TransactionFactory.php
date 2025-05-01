<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $customer = DB::table('users')->pluck('id');
        $sellers = DB::table('sellers')->pluck('id');
        return [
            'user_id' => $this->faker->randomElement($customer),
            'seller_id' => $this->faker->randomElement($sellers),
            'amount' => $this->faker->numberBetween($min = 1, $max = 1000),
            'post_balance' => $this->faker->numberBetween($min = 1, $max = 1000),
            'transaction_number' => trx_number(),
            'transaction_type' => '+',
            'details' => $this->faker->company(),
        ];
    }
}
