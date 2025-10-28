<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTradeOfferAmountColumnsToSaleOrderDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sale_order_data', function (Blueprint $table) {
            $table->decimal('trade_offer_amount' , 15 , 2)->nullable()->after('scheme_amount');

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
            $table->dropColumn('trade_offer_amount');
            //
        });
    }
}
