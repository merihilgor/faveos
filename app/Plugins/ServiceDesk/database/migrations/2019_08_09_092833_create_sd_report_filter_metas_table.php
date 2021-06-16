<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * report filter metas table to manage report filter fields
 * @author krishna vishwakarma <krishna.vishwakarma@ladybirdweb.com>
 */
class CreateSdReportFilterMetasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::create('sd_report_filter_metas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('report_filter_id')->unsigned();
            $table->string('key', 255);
            $table->longText('value_meta');
            $table->longText('value');
            $table->foreign('report_filter_id')->references('id')->on('sd_report_filters');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sd_report_filter_metas');
    }
}
