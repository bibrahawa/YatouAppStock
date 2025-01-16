<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Models\Setting;

class SiteinfoSeeder extends Seeder {


	public function run(){

		\Eloquent::unguard();


        \DB::table('settings')->truncate();

        $siteinfo = Setting::create(
            [
                'site_name' => 'Yatou Market',
                'slogan' => 'Best Online Shopping',
                'address' => 'Conakry, Guinea',
                'phone' => '+224 669 702 626',
                'email' => 'bibrah3@gmail.com',
                'owner_name' => 'Yatou Group Inc.',
                'currency_code' => 'GNF',
            ]
        );

    }

}
