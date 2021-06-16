<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * added team_id and department_id in sd_changes table to link team and department
 * @author krishna vishwakarma <krishna.vishwakarma@ladybirdweb.com>
 */
class AddTeamAndDepartmentInSdChangesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sd_changes', function (Blueprint $table) {
            $table->integer('team_id')->unsigned()->nullable();
            $table->foreign('team_id')->references('id')->on('teams');
            $table->integer('department_id')->unsigned()->nullable();
            $table->foreign('department_id')->references('id')->on('department');
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
            $table->dropForeign('sd_changes_team_id_foreign');
            $table->dropForeign('sd_changes_department_id_foreign');
            $table->dropColumn('team_id');
            $table->dropColumn('department_id');
        });
    }
}
