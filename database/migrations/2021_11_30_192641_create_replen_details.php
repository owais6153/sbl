<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReplenDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('replen_details', function (Blueprint $table) {
            $table->id();
            $table->integer('item_id');
            $table->string('item_name', 255);
            $table->string('urlid', 255)->nullable();
            $table->string('store_sku', 255)->nullable();
            $table->string('store', 255)->nullable();
            $table->integer('days_30_sales')->nullable();
            $table->integer('amazon_inventory')->nullable();
            $table->integer('unsellable')->nullable();
            $table->integer('on_hand_ridgefield')->nullable();
            $table->integer('amount_to_replen');
            $table->integer('replen_batch_id');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('replen_details');
    }
}
