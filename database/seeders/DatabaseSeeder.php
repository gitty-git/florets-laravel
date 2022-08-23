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
            'name' => 'Test User',
            'email' => 'e@mail.com',
            'password' => password_hash('12341234', PASSWORD_BCRYPT)
        ]);

        \App\Models\User::create([
            'name' => 'Test User 2',
            'email' => 'name@mail.com',
            'password' => password_hash('12341234', PASSWORD_BCRYPT),
            'role' => 'admin',
        ]);

        \App\Models\Product::factory(8)->create();
        \App\Models\Order::factory(60)->create();
        \App\Models\OpeningHours::create([
            'opens_at' => '09:00:00',
            'closes_at' => '23:59:59'
        ]);
    }
}
