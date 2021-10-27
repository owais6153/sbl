<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOnHandTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('on_hand', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('upload_id')->nullable();
            $table->foreign('upload_id')->references('id')->on('file_upload')->index();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->foreign('user_id')->references('id')->on('users');
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
        Schema::dropIfExists('on_hand');
    }
}
