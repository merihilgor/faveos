<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * sd_approver_statuses_table to link approval_level_status_id with sd_approver_statuses_table
 *
 * @author krishna vishwakarma <krishna.vishwakarma@ladybirdweb.com>
 */
class CreateSdApproverStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sd_approver_statuses', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('approver_id')->unsigned();
            $table->string('approver_type');
            $table->integer('approval_level_status_id')->unsigned();
            $table->foreign('approval_level_status_id')->references('id')->on('sd_approval_level_statuses');
            $table->string('status');
            $table->string('hash');
            $table->string('comment');
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
        Schema::table('sd_approver_statuses', function (Blueprint $table) {
            $table->dropForeign('sd_approver_statuses_approval_level_status_id_foreign');
        });

        Schema::dropIfExists('sd_approver_statuses');
    }
}
