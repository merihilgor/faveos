<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * sd_approval_workflow_changes_table to link change with approval workflow as cab
 *
 * @author krishna vishwakarma <krishna.vishwakarma@ladybirdweb.com>
 */
class CreateSdApprovalWorkflowChangesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sd_approval_workflow_changes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('approval_workflow_id')->unsigned();
            $table->foreign('approval_workflow_id')->references('id')->on('approval_workflows');
            $table->string('name', 255);
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('change_id')->unsigned();
            $table->foreign('change_id')->references('id')->on('sd_changes');
            $table->integer('action_on_approve');
            $table->integer('action_on_deny');
            $table->integer('change_status_id')->unsigned();
            $table->foreign('change_status_id')->references('id')->on('sd_change_status');
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
        Schema::table('sd_approval_workflow_changes', function (Blueprint $table) {
            $table->dropForeign('sd_approval_workflow_changes_approval_workflow_id_foreign');
            $table->dropForeign('sd_approval_workflow_changes_user_id_foreign');
            $table->dropForeign('sd_approval_workflow_changes_change_id_foreign');
            $table->dropForeign('sd_approval_workflow_changes_change_status_id_foreign');
        });

        Schema::dropIfExists('sd_approval_workflow_changes');
    }
}
