<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Dropped sd_cab table
 * Reason: At present no use, new CAB is implemented
 * @author krishna vishwakarma <krishna.vishwakarma@ladybirdweb.com>
 */
class DropSdCabVotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::drop('sd_cab_votes');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('sd_cab_votes', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('cab_id')->unsigned()->nullable()->index('sd_cab_votes_cab_id_foreign');
            $table->integer('user_id')->unsigned()->nullable()->index('sd_cab_votes_user_id_foreign');
            $table->string('comment');
            $table->string('owner');
            $table->integer('vote');
            $table->timestamps();
        });
    }
}
