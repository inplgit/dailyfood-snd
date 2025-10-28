<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChaneDataTypeIntoProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->integer('packing')->nullable()->default(1)->change();
            $table->integer('weight_uom_id')->nullable()->default(0)->change();
            $table->integer('locality')->nullable()->default(0)->change();
            $table->string('part')->nullable()->change();
            $table->decimal('width',$precision = 8, $scale = 2)->default(0)->nullable()->change();
            $table->decimal('height',$precision = 8, $scale = 2)->default(0)->nullable()->change();
            $table->decimal('length',$precision = 8, $scale = 2)->default(0)->nullable()->change();
            $table->string('color')->nullable()->change();
            $table->decimal('weight',$precision = 8, $scale = 2)->default(0)->nullable()->change();
            $table->decimal('reorder_qty',$precision = 8, $scale = 2)->default(0)->nullable()->change();
            $table->decimal('minimum_qty',$precision = 8, $scale = 2)->default(0)->nullable()->change();
            $table->decimal('maximum_qty',$precision = 8, $scale = 2)->default(0)->nullable()->change();
            $table->decimal('sales_price',$precision = 8, $scale = 2)->default(0)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
