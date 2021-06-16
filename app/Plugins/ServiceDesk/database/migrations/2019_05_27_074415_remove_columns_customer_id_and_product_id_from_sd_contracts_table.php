<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * remove customer_id and product_id from sd_contracts table
 * @author krishna vishwakarma <krishna.vishwakarma@ladybirdweb.com>
 */
class RemoveColumnsCustomerIdAndProductIdFromSdContractsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sd_contracts', function (Blueprint $table) {
            $table->dropForeign('sd_contracts_customer_id_foreign');
            $table->dropForeign('sd_contracts_product_id_foreign');
            $table->dropColumn('product_id');
            $table->dropColumn('customer_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sd_contracts', function (Blueprint $table) {
            $table->integer('customer_id')->unsigned()->nullable();
            $table->foreign('customer_id')->references('id')->on('users');
            $table->integer('product_id')->unsigned()->nullable();
            $table->foreign('product_id')->references('id')->on('sd_products');
        });

    }
}
