<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * alter sd_contracts column purpose_of_rejection to nullable
 * alter sd_contracts column contract_start_date type to datetime
 * alter sd_contracts column contract_end_date type to datetime
 * @author abhishek kumar shashi <abhishek.shahsi@ladybirdweb.com>
 */
class AlterSdContractTableColumnPurposeOfRejectionDefaultValueToNull extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sd_contracts', function (Blueprint $table) {
            $table->text('purpose_of_rejection')->nullable()->change();
            $table->datetime('contract_start_date')->charset(null)->change();
            $table->datetime('contract_end_date')->charset(null)->change();
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
            $table->text('purpose_of_rejection')->change();
            $table->string('contract_start_date')->change();
            $table->string('contract_end_date')->change();
        });
    }
}
