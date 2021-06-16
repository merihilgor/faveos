<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * created sd_emails_sources table for relation between third party emails
 * and contract 
 * @author abhishek kumar shashi <abhishek.shahsi@ladybirdweb.com>
 */
class CreateSdEmailsSourcesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sd_emails_sources', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('email_id')->unsigned();
            $table->foreign('email_id')->references('id')->on('sd_emails');
            $table->bigInteger('source_id')->unsigned();
            $table->string('source_type');
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
        Schema::dropIfExists('sd_emails_sources');
    }
}
