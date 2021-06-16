<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Dropped sd_location_categories table
 * Reason: At present no use
 * @author krishna vishwakarma <krishna.vishwakarma@ladybirdweb.com>
 */
class DropSdLocationCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::drop('sd_location_categories');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('sd_location_categories', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('parent_id')->nullable();
            $table->string('name');
            $table->timestamps();
        });
    }
}
