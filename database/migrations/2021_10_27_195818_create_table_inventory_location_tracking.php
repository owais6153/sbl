<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableInventoryLocationTracking extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventory_location_tracking', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('barcode', 255);
            $table->integer('quantity');
            $table->string('from', 255);
            $table->string('to', 255)->nullable();
            $table->date('expiration_date')->nullable();
            $table->string('pallet_number', 255)->nullable();
            $table->text('images')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inventory_location_tracking');
    }
}
