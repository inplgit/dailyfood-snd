<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReceiptVouchersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('receipt_vouchers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('distributor_id');
            $table->unsignedBigInteger('delivery_man_id');
            $table->unsignedBigInteger('tso_id');
            $table->unsignedBigInteger('route_id');
            $table->unsignedBigInteger('shop_id');
            $table->date('issue_date');
            $table->bigInteger('amount')->default(0);
            $table->string('mode_of_payment');
            $table->string('bank')->nullable();
            $table->string('cheque_no')->nullable();
            $table->date('cheque_date')->nullable();
            $table->boolean('execution')->default(false);
            $table->text('detail')->nullable();
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
        Schema::dropIfExists('receipt_vouchers');
    }
}
