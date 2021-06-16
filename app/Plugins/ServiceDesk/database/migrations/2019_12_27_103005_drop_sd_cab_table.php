<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropSdCabTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::drop('sd_cab');
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('sd_cab', function(Blueprint $table)
        {
            $table->increments('id');
            $table->string('name');
            $table->integer('head')->unsigned()->nullable()->index('sd_cab_head_foreign');
            $table->string('approvers')->nullable();
            $table->integer('aproval_mandatory');
            $table->timestamps();
        });
    }
}
