<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSdAlterChangeReleaseRelationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sd_change_release', function (Blueprint $table) {
            $table->dropForeign(['change_id']);
            $table->foreign('change_id')->references('id')->on('sd_changes')->onDelete('cascade');
            $table->dropForeign(['release_id']);
            $table->foreign('release_id')->references('id')->on('sd_releases')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sd_change_release');
    }
}
