<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
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
        $statuses = ['created', 'processed', 'sent', 'received', 'canceled'];
        $payments = ['cash', 'card'];
        $product = Product::inRandomOrder()->first();
        return [
            'status' => $statuses[rand(0, 4)],
            'delivery_time' =>  '2022-08-16 17:00:00',
            'paid' => rand(0, 1),
            'name' => fake()->name(),
            'comment' => fake()->sentence(),
            'phone' => fake()->phoneNumber(),
            'address' => 'Челябинск, Россия, 250 лет челябинска, 1',
            'payment_method' => $payments[rand(0,1)],
            'cart' => ('[{"amount": "2","id": "' . Product::inRandomOrder()->first()->id. '"}]')
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
