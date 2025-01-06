<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        $this->call(RolePermissionSeeder::class);
        $this->call(SiteinfoSeeder::class);
        $this->call(CustomerSeeder::class);
        $this->call(WareHouseSeeder::class);
        $this->call(VatRateSeeder::class);
        Model::reguard();
    }
}
