<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSaleTypeColumnsToSchemeProductData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('scheme_product_data', function (Blueprint $table) {
            $table->integer('sale_type')->nullable()->after('product_id');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('scheme_product_data', function (Blueprint $table) {
            $table->dropColumn('sale_type');
        });
    }
}
