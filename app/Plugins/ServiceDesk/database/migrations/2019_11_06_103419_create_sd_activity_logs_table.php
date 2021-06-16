<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * added sd_activity_logs table to accomodate servicedesk activity log
 * used package spatie/laravel-activitylog
 * table was named as activity_log by default from package
 *
 * @author krishna vishwakarma <krishna.vishwakarma@ladybirdweb.com>
 */
class CreateSdActivityLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sd_activity_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('batch')->nullable();
            $table->string('log_name')->nullable();
            $table->integer('source_id')->nullable();
            $table->string('source_type')->nullable();
            $table->integer('causer_id')->nullable();
            $table->string('causer_type')->nullable();
            $table->string('field_or_relation', 25)->nullable();
            $table->boolean('is_relation')->default(0);
            $table->text('initial_value')->nullable();
            $table->text('final_value')->nullable();
            $table->string('event_name', 25)->nullable();
            $table->timestamps();

            $table->index('log_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sd_activity_logs');
    }
}
