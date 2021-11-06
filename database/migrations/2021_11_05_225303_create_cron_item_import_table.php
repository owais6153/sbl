<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCronItemImportTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cron_item_import', function (Blueprint $table) {
            $table->id();
            $table->integer('remaining')->nullable();
            $table->integer('totalRecords')->nullable();
            $table->integer('item_offset')->nullable();
            $table->integer('item_limit')->nullable();
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
        Schema::dropIfExists('cron_item_import');
    }
}
