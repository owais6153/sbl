<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecivingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('on_reciving', function (Blueprint $table) {
            $table->id();
            $table->integer('upload_id')->index();
            $table->integer('user_id')->index();
            $table->string('brand', 255)->nullable();
            $table->string('item_number', 255)->index();
            $table->string('item_name', 255)->index();
            $table->string('warehouse', 255);
            $table->integer('on_hand');
            $table->integer('available');
            $table->integer('reserved');
            $table->integer('in_transit');
            $table->integer('on_sales_order');
            $table->integer('on_purchase_order');
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
        Schema::dropIfExists('on_reciving');
    }
}
