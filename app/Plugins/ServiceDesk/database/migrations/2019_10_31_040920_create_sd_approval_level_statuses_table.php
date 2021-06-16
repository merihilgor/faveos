<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * sd_approval_level_statuses_table to link change and approval level with approval level status
 *
 * @author krishna vishwakarma <krishna.vishwakarma@ladybirdweb.com>
 */
class CreateSdApprovalLevelStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sd_approval_level_statuses', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('approval_level_id')->unsigned();
            $table->foreign('approval_level_id')->references('id')->on('approval_levels');
            $table->integer('approval_workflow_change_id')->unsigned();
            $table->foreign('approval_workflow_change_id')->references('id')->on('sd_approval_workflow_changes');
            $table->string('name', 255);
            $table->string('match', 10);
            $table->tinyInteger('order')->unsigned();
            $table->boolean('is_active');
            $table->string('status');
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
        Schema::table('sd_approval_level_statuses', function (Blueprint $table) {
            $table->dropForeign('sd_approval_level_statuses_approval_level_id_foreign');
            $table->dropForeign('sd_approval_level_statuses_approval_workflow_change_id_foreign');
        });

        Schema::dropIfExists('sd_approval_level_statuses');
    }
}
