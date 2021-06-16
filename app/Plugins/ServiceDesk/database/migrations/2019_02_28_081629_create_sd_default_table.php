<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * sd_default_table to track default values of servicedesk
 * @author krishna vishwakarma <krishna.vishwakarma@ladybirdweb.com>
 */
class CreateSdDefaultTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sd_default', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('asset_type_id')->unsigned()->nullable();
            $table->timestamps();
        });

         Schema::table('sd_default', function (Blueprint $table) {
            $table->foreign('asset_type_id')->references('id')->on('sd_asset_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sd_default', function (Blueprint $table) {
            $table->dropForeign('asset_type_id');
        });
        Schema::dropIfExists('sd_default');
    }
}
