<?php

namespace Database\Seeders;

// use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\SiteinfoSeeder;
use Database\Seeders\CustomerSeeder;
use Database\Seeders\WareHouseSeeder;
use Database\Seeders\VatRateSeeder;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Model::unguard();
        $this->call(RolePermissionSeeder::class);
        $this->call(SiteinfoSeeder::class);
        // $this->call(CustomerSeeder::class);
        $this->call(WareHouseSeeder::class);
        $this->call(VatRateSeeder::class);
        // Model::reguard();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
