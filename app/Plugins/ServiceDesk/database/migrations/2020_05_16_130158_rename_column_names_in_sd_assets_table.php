<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameColumnNamesInSdAssetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sd_assets', function (Blueprint $table) {
            $table->dropForeign('sd_assets_managed_by_foreign');
            $table->dropIndex('sd_assets_managed_by_foreign');
            $table->dropForeign('sd_assets_used_by_foreign');
            $table->dropIndex('sd_assets_used_by_foreign');
        });
        
        Schema::table('sd_assets', function (Blueprint $table) {
            $table->dropForeign('sd_assets_attachment_foreign');
            $table->dropIndex('sd_assets_attachment_foreign');
        });

        Schema::table('sd_assets', function (Blueprint $table) {
            $table->renameColumn('external_id', 'identifier');
            $table->dropUnique(['external_id']);
        });

        Schema::table('sd_assets', function (Blueprint $table) {
            $table->datetime('assigned_on')->charset(null)->change();
        });

        Schema::table('sd_assets', function (Blueprint $table) {
            $table->renameColumn('organization', 'organization_id');
            $table->renameColumn('used_by', 'used_by_id');
            $table->renameColumn('managed_by', 'managed_by_id');
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
            $table->renameColumn('identifier', 'external_id');
        });

        Schema::table('sd_assets', function (Blueprint $table) {
            $table->timestamp('assigned_on');
        });

        Schema::table('sd_assets', function (Blueprint $table) {
            $table->integer('attachment')->unsigned()->nullable();
        });

        Schema::table('sd_assets', function (Blueprint $table) {
            $table->renameColumn('organization_id', 'organization');
            $table->renameColumn('used_by_id', 'used_by');
            $table->renameColumn('managed_by_id', 'managed_by');
        });
    }
}
