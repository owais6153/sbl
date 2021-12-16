<?php

namespace Database\Seeders;

use App\Models\Locations;
use Illuminate\Database\Seeder;

class LocationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $alphabets = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X');
        $aisles = array('A','B','C','D','E','F');
        $aisles2 = array('A','B','C');
        $locations=array();
        for($i=1;$i<=59;$i++){
            foreach($alphabets as $alpha){
                if($alpha == 'A' ||$alpha == 'B' ||$alpha == 'C' ||$alpha == 'D' ||$alpha == 'E' ||$alpha == 'F' ||$alpha == 'X' ){
                    foreach($aisles as $alis){
                        $locations[] = $alpha.$i.$alis;
                    }
                }else{
                    foreach($aisles2 as $alis2){
                        $locations[] = $alpha.$i.$alis2;
                    }
                }
            }
        }
        foreach($locations as $locate){
            $local = new Locations();
            $local->locations = $locate;
            $local->save();
        }
    }
}
