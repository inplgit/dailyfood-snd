<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnIntoProduct extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('product_code')->after('name');
            $table->integer('category_id')->after('product_code');
            $table->integer('brand_id')->after('category_id');
            $table->integer('uom_id')->after('brand_id');
            $table->integer('weight_uom_id')->after('uom_id');
            $table->Boolean ('locality')->after('weight_uom_id')->default(1);
            $table->string('SKU')->after('weight_uom_id');
            $table->string('packing')->after('weight_uom_id')->default(1);
            $table->boolean('qc_reuired')->after('weight_uom_id')->default(0);
            $table->string('part')->after('weight_uom_id')->nullable;
            $table->decimal('width',$precision = 8, $scale = 2)->after('weight_uom_id')->default(0);
            $table->decimal('height',$precision = 8, $scale = 2)->after('weight_uom_id')->default(0);
            $table->decimal('length',$precision = 8, $scale = 2)->after('weight_uom_id')->default(0);
            $table->string('color')->after('weight_uom_id')->nullable;
            $table->decimal('weight',$precision = 8, $scale = 2)->after('weight_uom_id')->default(0);
            $table->decimal('reorder_qty',$precision = 8, $scale = 2)->after('weight_uom_id')->default(0);
            $table->decimal('minimum_qty',$precision = 8, $scale = 2)->after('weight_uom_id')->default(0);
            $table->decimal('maximum_qty',$precision = 8, $scale = 2)->after('weight_uom_id')->default(0);
            $table->decimal('sales_price',$precision = 8, $scale = 2)->after('weight_uom_id')->default(0);

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
