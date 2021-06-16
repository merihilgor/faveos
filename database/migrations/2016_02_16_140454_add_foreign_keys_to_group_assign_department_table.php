<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToGroupAssignDepartmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('group_assign_department', function (Blueprint $table) {
            $table->foreign('group_id', 'group_assign_department_ibfk_1')->references('id')->on('groups')->onUpdate('NO ACTION')->onDelete('RESTRICT');
            $table->foreign('department_id', 'group_assign_department_ibfk_2')->references('id')->on('department')->onUpdate('NO ACTION')->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('group_assign_department');
    }
}
