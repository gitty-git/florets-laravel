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
        $payments = ['cash', 'card', 'online'];
        $wayToReceive = ['me', 'another', 'self_delivery'];
        return [
            'payment_method' => $payments[rand(0, 2)],
            'delivery_time' =>  '2022-11-25T08:00:00.000Z',
            'name' => fake()->name(),
            'comment' => fake()->sentence(),
            'phone' => fake()->phoneNumber(),
            'receiver_name' => fake()->name(),
            'receiver_phone' => fake()->phoneNumber(),
            'apt' => 32,
            'way_to_receive' => $wayToReceive[rand(0, 2)],
            'address' => 'Челябинск, Россия, 250 лет челябинска, 1',
            'cart' => ('[{"id": 8, "size": "xl", "slug": "xl", "price": 6500, "amount": 1, "images": "[\"http://127.0.0.1:8000/storage/images/products//YRcyg9r6ulPLPytoUKVgUDvq6UJve5iK1hdUWP4c.jpg\", \"http://127.0.0.1:8000/storage/images/products//7i3b7Puxg9uDOGAX5Sd3lnhSa4GSn7wsTAYVbGhL.jpg\", \"http://127.0.0.1:8000/storage/images/products//rR3yzTU8rX5CXQGyivJAQnLWCquCCI2wGsmdA6Ch.jpg\", \"http://127.0.0.1:8000/storage/images/products//ym4BFYI6i0MaBOj2GghlPNeQnThwKsCXO7Gt3hNf.jpg\"]", "published": 1, "created_at": "2022-11-23T09:17:21.000000Z", "product_id": 7, "updated_at": "2022-11-23T09:17:21.000000Z", "description": "[\"Для важных событий.\", \"Для наставников и руководителей.\", \"Для тех, кого хочется удивить.\"]", "productName": "Pink", "productSlug": "pink"}]')
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
