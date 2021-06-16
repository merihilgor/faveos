<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Pivot table for sd_contracts and users
 * @author krishna vishwakarma <krishna.vishwakarma@ladybirdweb.com>
 */
class CreateSdContractUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sd_contract_user', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('contract_id')->unsigned();
            $table->integer('agent_id')->unsigned();
            $table->timestamps();
        });
        
        Schema::table('sd_contract_user', function($table) {
          $table->foreign('contract_id')->references('id')->on('sd_contracts');
          $table->foreign('agent_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sd_contract_user', function (Blueprint $table) {
            $table->dropForeign('contract_id');
            $table->dropForeign('agent_id');
        });
    
        Schema::dropIfExists('sd_contract_user');
    }
}
