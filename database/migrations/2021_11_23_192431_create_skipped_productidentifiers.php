<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSkippedProductidentifiers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('skipped_itemidentifiers', function (Blueprint $table) {
            $table->id();
            $table->integer('item_id');
            $table->integer('identifier_id');
            $table->integer('duplicate_item_id');
            $table->string('barcode', 255);
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
        Schema::dropIfExists('skipped_itemidentifiers');
    }
}
