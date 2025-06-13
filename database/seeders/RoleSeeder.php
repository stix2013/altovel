<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Role; // Ensure Role model is imported

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if roles already exist to prevent duplicates if seeder is run multiple times
        if (Role::count() == 0) {
            Role::create(['name' => 'Admin']);
            Role::create(['name' => 'Editor']);
            Role::create(['name' => 'Viewer']);
        }
        // Or, using DB facade if you prefer, ensuring to handle potential duplicates or use firstOrCreate
        // DB::table('roles')->insertOrIgnore([
        //     ['name' => 'Admin'],
        //     ['name' => 'Editor'],
        //     ['name' => 'Viewer'],
        // ]);
    }
}
