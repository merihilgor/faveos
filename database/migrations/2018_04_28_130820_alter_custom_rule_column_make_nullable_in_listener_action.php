<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCustomRuleColumnMakeNullableInListenerAction extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('listener_actions', function (Blueprint $table) {
            $table->longText('custom_action')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('listener_actions', function (Blueprint $table) {
            $table->dropIfExists('custom_action');
        });
    }
}
