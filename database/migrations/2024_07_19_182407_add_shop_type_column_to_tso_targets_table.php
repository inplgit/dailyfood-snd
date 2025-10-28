<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddShopTypeColumnToTsoTargetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tso_targets', function (Blueprint $table) {
            $table->integer('shop_type')->nullable()->after('amount');
            $table->integer('shop_qty')->nullable()->after('shop_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tso_targets', function (Blueprint $table) {
            $table->integer('shop_type')->nullable()->after('amount');
            $table->integer('shop_qty')->nullable()->after('shop_type');
        });
    }
}
