<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdaateInventoryLocationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inventory_location', function (Blueprint $table) {

            $table->date('expiration_date')->nullable()->after('inventory_track_id');


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inventory_location', function (Blueprint $table) {
             $table->dropColumn('expiration_date');

        });
    }
}
