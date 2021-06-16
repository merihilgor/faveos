<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
/**
 * changed identifier data type in sd_contracts_table
 * @author krishna vishwakarma <krishna.vishwakarma@ladybirdweb.com>
 */
class AlterIdentifierDataTypeInSdContractsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sd_contracts', function (Blueprint $table) {
            $table->string('identifier')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sd_contracts', function (Blueprint $table) {
            $table->integer('identifier')->unsigned()->nullable()->change();
        });
    }
}
