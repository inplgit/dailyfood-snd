<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableTsoTargetsChangeProductIdColomnType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tso_targets', function (Blueprint $table) {
            $table->bigInteger('product_id')->nullable()->change();
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
            $table->bigInteger('product_id')->nullable()->change();
        });
    }
}
