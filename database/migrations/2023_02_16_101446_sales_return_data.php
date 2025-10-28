<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SalesReturnData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_return_data', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('sales_return_id')->constrained('sales_returns');
            $table->foreignId('sales_order_data_id')->constrained('sale_order_data');
            $table->decimal('qty',15,2)->default(0);
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
        Schema::dropIfExists('sales_return_data');
    }
}
