<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSchemeColumnsToSaleOrderDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sale_order_data', function (Blueprint $table) {
            $table->integer('scheme_data_id')->nullable()->after('offer_qty');
            $table->decimal('scheme_amount' , 15 , 2)->nullable()->after('scheme_data_id');

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
            $table->dropColumn('scheme_data_id');
            $table->dropColumn('scheme_amount');

        });
    }
}
