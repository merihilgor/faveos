<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Table to manage contract history
 * @author krishna vishwakarma <krishna.vishwakarma@ladybirdweb.com>
 */
class CreateSdContractThreadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sd_contract_threads', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('contract_id')->unsigned();
            $table->integer('status_id')->unsigned();
            $table->integer('renewal_status_id')->unsigned()->nullable();
            $table->integer('cost');
            $table->timestamp('contract_start_date');
            $table->timestamp('contract_end_date');
            $table->integer('owner_id')->unsigned();
            $table->integer('approver_id')->unsigned();
            $table->timestamps();
        });

        Schema::table('sd_contract_threads', function (Blueprint $table) {
            $table->foreign('contract_id')->references('id')->on('sd_contracts');
            $table->foreign('status_id')->references('id')->on('sd_contract_statuses');
            $table->foreign('renewal_status_id')->references('id')->on('sd_contract_statuses');
            $table->foreign('owner_id')->references('id')->on('users');
            $table->foreign('approver_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sd_contract_threads', function (Blueprint $table) {
            $table->dropForeign('contract_id');
            $table->dropForeign('status_id');
            $table->dropForeign('renewal_status_id');
            $table->dropForeign('owner_id');
            $table->dropForeign('approver_id');
        });
        
        Schema::dropIfExists('sd_contract_threads');
    }
}