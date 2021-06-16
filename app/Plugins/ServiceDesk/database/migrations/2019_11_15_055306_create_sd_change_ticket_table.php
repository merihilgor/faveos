<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSdChangeTicketTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sd_change_ticket', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('change_id')->unsigned()->nullable();
            $table->foreign('change_id')->references('id')->on('sd_changes');
            $table->integer('ticket_id')->unsigned()->nullable();
            $table->foreign('ticket_id')->references('id')->on('tickets');
            $table->string('type',20)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $table->dropForeign('sd_change_ticket_change_id_foreign');
        $table->dropForeign('sd_change_ticket_ticket_id_foreign');
        Schema::dropIfExists('sd_change_ticket');
    }
}