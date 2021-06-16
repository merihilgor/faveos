<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Added type column in sd_contract_statuses table to separate contract status 
 * and renewal status
 * @author krishna vishwakarma <krishna.vishwakarma@ladybirdweb.com>
 */
class AddTypeColumnInSdContractStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sd_contract_statuses', function (Blueprint $table) {
            $table->string('type', 20);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sd_contract_statuses', function (Blueprint $table) {
            $table->dropColumn('type'); 
        });
    }
}
