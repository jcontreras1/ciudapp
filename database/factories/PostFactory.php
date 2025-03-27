<?php

namespace Database\Factories;

use App\Models\City;
use App\Models\Subcategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{

    protected $model = \App\Models\Post::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => 1,
            'lat' => fake()->latitude(-42.8, -42.6),
            'lng' => fake()->longitude(-65.1, -64.9),
            'comment' => fake()->sentence(),
            'private' => false,
            'subcategory_id' => Subcategory::inRandomOrder()->first()->id,
            'city_id' => City::where('name', 'like', '%Madryn%')->first()->id ?? null
        ];
    }
}
