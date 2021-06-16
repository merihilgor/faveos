<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSdAlterReleaseTable extends Migration
{
   /**
     * Run the migrations.
     *
     * @return void
     */
     public function up()
    {
        Schema::table('sd_releases', function (Blueprint $table) {
        
        $table->dropForeign(['location_id']);
        $table->foreign('location_id')->references('id')->on('location');
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sd_releases');
    }
}

