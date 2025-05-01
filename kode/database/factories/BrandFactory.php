<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Brand;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Brand>
 */
class BrandFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     *
     */
    protected $model = Brand::class;


    public function definition()
    {
        return [
            'name'  => $this->faker->company(),
            'logo'  => null,
            'status'=>$this->faker->numberBetween($min = 0, $max = 1)
        ];
    }
}
