<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRackScansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rack_scans', function (Blueprint $table) {
            // $table->id();
            $table->integer('rack_id');
            $table->integer('shop_id');
            $table->integer('tso_id');
            $table->tinyInteger('rack_status')->nullable();
            $table->string('remarks')->nullable();
            $table->tinyInteger('status')->default(1);

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
        Schema::dropIfExists('rack_scans');
    }
}
