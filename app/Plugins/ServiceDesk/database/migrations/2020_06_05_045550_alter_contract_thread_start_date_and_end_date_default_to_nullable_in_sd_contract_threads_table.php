<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * alter sd_contract_threads table column contract_start_date and 
 * contract_end_date and approver_id column to nullable
 * @author abhishek kumar shashi <abhishek.shahsi@ladybirdweb.com>
 */
class AlterContractThreadStartDateAndEndDateDefaultToNullableInSdContractThreadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sd_contract_threads', function (Blueprint $table) {
            $table->datetime('contract_start_date')->nullable()->default(null)->change();
            $table->datetime('contract_end_date')->nullable()->default(null)->change();
        });

        Schema::table('sd_contract_threads', function (Blueprint $table) {
            $table->integer('approver_id')->unsigned()->nullable()->change();
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
            $table->timestamp('contract_start_date')->change();
            $table->timestamp('contract_end_date')->change();
        });

        Schema::table('sd_contract_threads', function (Blueprint $table) {
            $table->integer('approver_id')->unsigned()->change();
        });
    }
}
