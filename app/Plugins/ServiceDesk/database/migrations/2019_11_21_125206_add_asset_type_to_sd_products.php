<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * added asset_type_id
 * @author Abhishek Kumar Shashi <abhishek.shashi@ladybirdweb.com>
 */

class AddAssetTypeToSdProducts extends Migration
{
    
    public function up()
    {
        Schema::table('sd_products', function (Blueprint $table) {
            $table->integer('asset_type_id')->unsigned()->nullable();
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
        Schema::table('sd_products', function (Blueprint $table) {
            $table->dropForeign('sd_products_asset_type_id_foreign');
            $table->dropColumn('asset_type_id'); 
        });
    }
}
