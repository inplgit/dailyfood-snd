<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSaleOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sale_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('distributor_id');
            $table->unsignedBigInteger('tso_id');
            $table->unsignedBigInteger('shop_id');
            $table->foreignId('user_id')->constrained('users');
            $table->bigInteger('invoice_no')->unique();
            $table->bigInteger('dc_no')->nullable();
            $table->bigInteger('delivery_date')->nullable();
            $table->bigInteger('lpo_no')->nullable();
            $table->date('dc_date')->nullable();
            $table->boolean('excecution')->default(false);
            $table->date('excecution_date')->nullable();
            $table->enum('payment_type', ['cash', 'credit']);
            $table->integer('total_carton')->default(0);            
            $table->integer('total_pcs')->default(0);            
            $table->string('cost_center');            
            $table->text('notes')->nullable();            
            $table->text('transport_details');            
            $table->integer('discount_percent')->default(0);            
            $table->decimal('discount_amount', 10, 2)->default(0);            
            $table->decimal('tax_applied', 10, 2)->default(0);
            $table->decimal('pending_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->decimal('products_subtotal', 10, 2)->default(0);
            $table->decimal('old_receivable', 10, 2)->default(0);
            $table->decimal('freight_charges', 10, 2)->default(0);
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
        Schema::dropIfExists('sale_orders');
    }
}
