<?php

namespace Database\Factories;

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
            'lat' => fake()->latitude(),
            'lng' => fake()->longitude(),
            'comment' => fake()->sentence(),
            'private' => false,
            'subcategory_id' => Subcategory::inRandomOrder()->first()->id,
        ];
    }
}
