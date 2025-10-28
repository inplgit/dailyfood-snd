<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSaleOrderDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sale_order_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('so_id')->constrained('sale_orders');
            $table->unsignedBigInteger('product_id');
            $table->decimal('rate', 10, 2)->default(0);
            $table->decimal('qty', 10, 2)->default(0);
            $table->integer('discount')->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->integer('tax_percent')->default(0);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->smallInteger('status')->default(1)->comment('0=ACTIVE 1=NOT ACTIVE');
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
        Schema::dropIfExists('sale_order_data');
    }
}
