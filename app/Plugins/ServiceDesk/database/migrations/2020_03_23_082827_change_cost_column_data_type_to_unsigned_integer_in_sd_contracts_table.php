<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Changed cost data type column to duble from var char
 * @author krishna vishwakarma <krishna.vishwakarma@ladybirdweb.com>
 */
class ChangeCostColumnDataTypeToUnsignedIntegerInSdContractsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sd_contracts', function (Blueprint $table) {
            $table->unsignedInteger('cost')->charset('')->nullable()->change();
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
            $table->string('cost')->change();
        });
    }
}
