<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateListenerEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('listener_events', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('listener_id');
            $table->string('event');
            $table->string('condition');
            $table->string('old')->nullable();
            $table->string('new')->nullable();
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
        Schema::dropIfExists('listener_events');
    }
}
