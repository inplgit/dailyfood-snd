<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRouteTsoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('route_tso', function (Blueprint $table) {
            $table->id();
            $table->integer('route_id');
            $table->integer('tso_id');
            $table->timestamps();

           // $table->foreign('route_id')->references('id')->on('routes')->onDelete('cascade');
          //  $table->foreign('tso_id')->references('id')->on('tsos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('route_tso');
    }
}
