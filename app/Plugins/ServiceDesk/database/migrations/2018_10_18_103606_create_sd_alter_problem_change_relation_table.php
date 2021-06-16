<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSdAlterProblemChangeRelationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sd_problem_change', function (Blueprint $table) {
            $table->dropForeign(['problem_id']);
            $table->foreign('problem_id')->references('id')->on('sd_problem')->onDelete('cascade');
            $table->dropForeign(['change_id']);
            $table->foreign('change_id')->references('id')->on('sd_changes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sd_problem_change');
    }
}
