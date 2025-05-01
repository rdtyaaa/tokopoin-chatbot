<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Withdraw>
 */
class WithdrawFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $sellers = DB::table('sellers')->pluck('id');
        $currencies = DB::table('currencies')->pluck('id');
        return [
            'seller_id' => $this->faker->randomElement($sellers),
            'method_id' => 1,
            'currency_id' => $this->faker->randomElement($currencies),
            'amount' => $this->faker->numberBetween($min = 1, $max = 1000),
            'charge' => $this->faker->numberBetween($min = 1, $max = 100),
            'trx_number' => trx_number(),
            'final_amount' =>  $this->faker->numberBetween($min = 1, $max = 1000),
            'status' => $this->faker->numberBetween($min = 1, $max = 3),
        ];
    }
}
