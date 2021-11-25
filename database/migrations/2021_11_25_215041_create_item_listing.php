<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemListing extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_listing', function (Blueprint $table) {
            $table->id();
            $table->integer('item_id');
            $table->string('_id', 255)->nullable();
            $table->string('storeSKU', 255)->nullable();
            $table->string('listingId', 255)->nullable();
            $table->string('fnSKU', 255)->nullable();
            $table->string('listingName', 255)->nullable();
            $table->string('store', 255)->nullable();
            $table->string('urlId', 255)->nullable();
            $table->string('fulfilledBy', 255)->nullable();
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
        Schema::dropIfExists('item_listing');
    }
}
