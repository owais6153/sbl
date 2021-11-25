<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateInventoryLocationAddItemId extends Migration
{
    public function up()
    {
        Schema::table('inventory_location_tracking', function (Blueprint $table) {
            $table->integer('item_id')->nullable()->after('barcode');
        });

        Schema::table('inventory_location', function (Blueprint $table) {
            $table->integer('item_id')->nullable()->after('from_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inventory_location_tracking', function (Blueprint $table) {
             $table->dropColumn('item_id');
        });
        Schema::table('inventory_location', function (Blueprint $table) {
             $table->dropColumn('item_id');
        });
    }
}
