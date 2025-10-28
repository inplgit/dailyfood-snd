<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSchemeIdColumnsToSaleOrderData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sale_order_data', function (Blueprint $table) {
            $table->integer('scheme_id')->nullable()->after('offer_qty');
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
            $table->dropColumn('scheme_id');
            //
        });
    }
}
