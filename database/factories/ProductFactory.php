<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => fake()->name(),
            'main_image' => rand(1, 3),
            'slug' => 'red',
            // 'images' => json_encode(rand(1, 3)),
            // 'published' => fake()->boolean(),
            // 'price' => rand(20, 30) * 100,
            'description' =>fake()->text,
            'composition' => fake()->text,
            // 'size' => rand(20, 40),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return static
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }
}
