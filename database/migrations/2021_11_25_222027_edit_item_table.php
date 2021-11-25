<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EditItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('item', function (Blueprint $table) {
             $table->string('avg_cost_source', 255)->nullable()->after('item_number');
             $table->float('avg_cost')->nullable()->after('item_number');
             $table->string('ridgefield_onhand', 255)->nullable()->after('item_number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('item', function (Blueprint $table) {
             $table->dropColumn('avg_cost_source');
             $table->dropColumn('avg_cost');
             $table->dropColumn('ridgefield_onhand');
        });
    }
}
