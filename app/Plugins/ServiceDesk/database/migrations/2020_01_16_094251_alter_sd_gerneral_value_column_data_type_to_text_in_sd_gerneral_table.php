<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Changed sd_gerneral table data type string to text in sd_gerneral table
 * @author krishna vishwakarma <krishna.vishwakarma@ladybirdweb.com>
 */
class AlterSdGerneralValueColumnDataTypeToTextInSdGerneralTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sd_gerneral', function (Blueprint $table) {
            $table->text('value')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sd_gerneral', function (Blueprint $table) {
            $table->string('value')->change();
        });
    }
}
