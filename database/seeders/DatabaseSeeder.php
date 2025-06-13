<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->asAdmin()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            // Password will be 'password' by default via factory
        ]);

        User::factory(10)->asCustomer()->create();

        // You might also want a guest user example if applicable
        // \App\Models\User::factory()->asGuest()->create([
        //     'name' => 'Guest User',
        //     'email' => 'guest@example.com',
        // ]);
    }
}
