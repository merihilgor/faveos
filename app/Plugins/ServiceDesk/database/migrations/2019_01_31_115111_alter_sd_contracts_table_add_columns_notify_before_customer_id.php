<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * added new columns notify_before and customer_id for contract notification
 * @author krishna vishwakarma <krishna.vishwakarma@ladybirdweb.com>
 */
class AlterSdContractsTableAddColumnsNotifyBeforeCustomerId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sd_contracts', function (Blueprint $table) {
            $table->integer('notify_before')->nullable();
            $table->integer('customer_id')->unsigned()->nullable();
        });

        Schema::table('sd_contracts', function (Blueprint $table) {
            $table->foreign('customer_id')->references('id')->on('users');
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
            $table->dropForeign('customer_id');
        });
        
        Schema::table('sd_contracts', function (Blueprint $table) {
            $table->dropColumn('notify_before');
            $table->dropForeign('customer_id');
        });

    }
}
