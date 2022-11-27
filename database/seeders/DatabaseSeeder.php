<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        \App\Models\User::create([
            'name' => 'Nikita',
            'email' => 'nikita@mail.com',
            'role' => 'admin',
            'password' => password_hash('jdUJDu63Y', PASSWORD_BCRYPT)
        ]);

        // \App\Models\Product::factory(4)->create();
        // \App\Models\Order::factory(20)->create();
        \App\Models\OpeningHours::create([
            'opens_at' => '08:00:00',
            'closes_at' => '23:59:59'
        ]);
    }
}
