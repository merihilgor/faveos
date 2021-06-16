<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SdAssetChanges extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sd_assets', function (Blueprint $table) {
        
        $table->string('assigned_on')->nullable()->change();
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
        $table->string('assigned_on')->nullable(false);
    });
    }
}
