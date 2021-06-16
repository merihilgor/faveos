<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Changes requester column name to requester_id in sd_changes table
 * naming convention correction and for better use in spatie/activity log
 *
 * @author krishna vishwakarma <krishna.vishwakarma@ladybirdweb.com>
 */
class AlterRequesterColumnNameToRequesterIdInSdChangesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sd_changes', function (Blueprint $table) {
            $table->renameColumn('requester', 'requester_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sd_changes', function (Blueprint $table) {
            $table->renameColumn('requester_id', 'requester');
        });
    }
}
