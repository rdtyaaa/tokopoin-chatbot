<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = Category::class;


    public function definition()
    {
        $categoryid = DB::table('categories')->pluck('id');
        return [
            'name'             => $this->faker->unique()->name,
            'parent_id'        => $this->faker->numberBetween($min = 1, $max = 15),
            'banner'           => null,
            'meta_title'       => $this->faker->text(),
            'meta_description' => $this->faker->text(),
            'meta_image' => null,
            'status' =>$this->faker->numberBetween($min = 0, $max = 1)
        ];
    }
}
