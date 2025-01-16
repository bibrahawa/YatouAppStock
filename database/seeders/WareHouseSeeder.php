<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Models\Warehouse;

class WareHouseSeeder extends Seeder {


	public function run(){

		\Eloquent::unguard();


        \DB::table('warehouses')->truncate();

        $warehouse = Warehouse::create(
            [
                'name' => 'Supermarcher Yatou',
                'address' => "Rond Point D'odienne",
                'phone' => '+225 05 06 00 00 26',
                'email' => 'alhassane@yatou.store',
                'in_charge_name' => 'Alhassane Barry',
            ]
        );

    }

}
