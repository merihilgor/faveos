<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBarcodeTemplates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('barcode_templates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('width');
            $table->string('height');
            $table->string('labels_per_row');
            $table->string('space_between_labels');
            $table->text('logo_image');
            $table->integer('display_logo_confirmed');
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
        Schema::dropIfExists('barcode_templates');
    }
}
