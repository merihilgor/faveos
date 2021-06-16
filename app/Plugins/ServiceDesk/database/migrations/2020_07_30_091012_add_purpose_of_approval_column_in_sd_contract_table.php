<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Added purpose of approval column in sd_contracts table
 *
 */
class AddPurposeOfApprovalColumnInSdContractTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sd_contracts', function (Blueprint $table) {
            $table->text('purpose_of_approval')->nullable();
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
            $table->dropColumn('purpose_of_approval'); 
        });
    }
}
