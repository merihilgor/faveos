<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * changed description column data type to text in sd_changes table
 * @author krishna vishwakarma <krishna.vishwakarma@ladybirdweb.com>
 */
class AlterSdChangesDescriptionColumnDataTypeToTextInSdChangesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sd_changes', function (Blueprint $table) {
            $table->text('description')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sd_changes', function (Blueprint $table) {
            $table->string('description')->change();
        });
    }
}
