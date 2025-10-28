<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class NullableTSOColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tso', function (Blueprint $table) {
            $table->string('state')->nullable()->change();
            $table->string('country')->nullable()->change();
            $table->unsignedBigInteger('manager')->nullable()->change();
            $table->unsignedBigInteger('kpo')->nullable()->change();
            $table->unsignedBigInteger('kpo_2')->nullable()->change();
            $table->unsignedBigInteger('kpo_3')->nullable()->change();
            $table->unsignedBigInteger('department_id')->comment('belongs to department model')->nullable()->change();
            $table->unsignedBigInteger('designation_id')->comment('belongs to designation model')->nullable()->change();
            $table->unsignedInteger('spot_sale')->comment('0=No 1=Yes')->nullable()->change();
            $table->unsignedInteger('auto_payment')->comment('0=No 1=Yes')->nullable()->change();
            $table->unsignedBigInteger('geography_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
     //   Schema::dropIfExists('tso');
    }
}
