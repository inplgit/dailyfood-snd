<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->string('voucher_no')->nullable();
            $table->date('voucher_date');
            $table->integer('product_id');
            $table->integer('distributor_id');
            $table->integer('stock_received_type')->comment('1 =stock from principle');
            $table->integer('stock_type')->comment('1 = damag stock ,  2 = expired stock');
            $table->longtext('remarks')->nullable();
            $table->decimal('qty',10 , 2)->default(0);
            $table->decimal('amunt',10 , 2)->default(0);
            $table->boolean('status')->default(1);
            $table->string('username');
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
        Schema::dropIfExists('stocks');
    }
}
