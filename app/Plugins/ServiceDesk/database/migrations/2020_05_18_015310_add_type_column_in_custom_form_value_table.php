<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** added type column in custom_form_value for differentiating form values
    form values of asset type, asset and  department could be differentiated
    similary, values could be seperated for other modules too
    whenever formbuilder is implemented in other modules
    it will be helpful in displaying custom (field name, value) in asset view page
*/
class AddTypeColumnInCustomFormValueTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('custom_form_value', function (Blueprint $table) {
            $table->string('type', 33);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('custom_form_value', function (Blueprint $table) {
            $table->dropColumn('type', 33); 
        });
    }
}
