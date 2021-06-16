<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameBarcodeTemplatesTableToSdBarcodeTemplates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('barcode_templates')) {
            Schema::rename('barcode_templates', 'sd_barcode_templates');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('sd_barcode_templates')) {
            Schema::rename('sd_barcode_templates', 'barcode_templates');
        }
    }
}
