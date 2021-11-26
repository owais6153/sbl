<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Bouncer;
class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions =array('view_all_users','user_add','user_update','user_delete','view_all_role','role_add','role_update','role_delete','view_all_item','item_skip','inventory_view_on_hand','inventory_view_on_receive','inventory_import','scan_inventroy','inventory_location','inventory_adjustment');
        foreach($permissions as $per){
           Bouncer::allow('admin')->to($per);

        }
    }
}
