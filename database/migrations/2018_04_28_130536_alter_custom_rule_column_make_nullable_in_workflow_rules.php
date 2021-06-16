<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCustomRuleColumnMakeNullableInWorkflowRules extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('workflow_rules', function (Blueprint $table) {
            $table->longText('custom_rule')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('workflow_rules', function (Blueprint $table) {
            $table->longText('custom_rule')->nullable(false)->change();
        });
    }
}
