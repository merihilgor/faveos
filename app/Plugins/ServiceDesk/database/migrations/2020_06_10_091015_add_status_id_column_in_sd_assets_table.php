<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Added status_id column in sd_assets table for implementing asset status functionality
 *
 */
class AddStatusIdColumnInSdAssetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sd_assets', function (Blueprint $table) {
            $table->unsignedInteger('status_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sd_assets', function (Blueprint $table) {
            $table->dropColumn('status_id'); 
        });
    }
}
