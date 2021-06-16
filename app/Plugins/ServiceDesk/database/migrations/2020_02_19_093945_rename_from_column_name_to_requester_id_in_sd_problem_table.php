<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameFromColumnNameToRequesterIdInSdProblemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sd_problem', function (Blueprint $table) {
            $table->renameColumn('from', 'requester_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sd_problem', function (Blueprint $table) {
             $table->renameColumn('requester_id', 'from');
        });
    }
}
