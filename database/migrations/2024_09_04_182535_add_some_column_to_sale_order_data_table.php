<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSomeColumnToSaleOrderDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sale_order_data', function (Blueprint $table) {
            $table->integer('flavour_id')->after('product_id');
            $table->integer('sale_type')->after('flavour_id')->comment('1 = piece , 2 = packet , 3 = Carton');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sale_order_data', function (Blueprint $table) {
            $table->dropColumn('flavour_id');
            $table->dropColumn('sale_type');
        });
    }
}
