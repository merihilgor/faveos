<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * added new columns status_id, renewal_status_id, purpose_of_rejection and owner_id for contract notification
 * made license count column as nullable
 * @author krishna vishwakarma <krishna.vishwakarma@ladybirdweb.com>
 */
class AlterSdContractsTableAddColumnsStatusIdRenewalStatusIdPurposeOfRejectionOwnerId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sd_contracts', function (Blueprint $table) {
            $table->integer('status_id')->unsigned()->nullable();
            $table->integer('renewal_status_id')->unsigned()->nullable();
            $table->text('purpose_of_rejection');
            $table->integer('owner_id')->unsigned();
            $table->integer('licensce_count')->nullable()->change();
        });

        Schema::table('sd_contracts', function (Blueprint $table) {
            $table->foreign('status_id')->references('id')->on('sd_contract_statuses');
            $table->foreign('renewal_status_id')->references('id')->on('sd_contract_statuses');
            $table->foreign('owner_id')->references('id')->on('users');
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
            $table->dropForeign('status_id');
            $table->dropForeign('renewal_status_id');
            $table->dropForeign('owner_id');
        });
        
        Schema::table('sd_contracts', function (Blueprint $table) {
            $table->dropColumn('status_id');
            $table->dropColumn('renewal_status_id');
            $table->dropColumn('purpose_of_description');
            $table->dropForeign('owner_id');
            $table->integer('licensce_count')->change();
        });

    }
}
