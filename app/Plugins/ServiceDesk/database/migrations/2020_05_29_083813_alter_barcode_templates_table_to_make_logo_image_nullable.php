<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterBarcodeTemplatesTableToMakeLogoImageNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('barcode_templates', function (Blueprint $table) {
            $table->text('logo_image')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('barcode_templates', function (Blueprint $table) {
            $table->text('logo_image')->nullable(false)->change();
        });
    }
}
