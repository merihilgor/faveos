<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSdAssetTypeFormGroupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sd_asset_type_form_group', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('form_group_id')->nullable();
            $table->foreign('form_group_id')->references('id')->on('form_groups');
            $table->unsignedInteger('asset_type_id')->nullable();
            $table->foreign('asset_type_id')->references('id')->on('sd_asset_types');
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
        Schema::table('sd_asset_type_form_group', function (Blueprint $table) {
            $table->dropForeign('sd_asset_type_form_group_form_group_id_foreign');
            $table->dropForeign('sd_asset_type_form_group_asset_type_id_foreign');
        });
        Schema::dropIfExists('sd_asset_type_form_group');
    }
}
