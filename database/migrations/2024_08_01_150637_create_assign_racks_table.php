<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssignRacksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assign_racks', function (Blueprint $table) {
            $table->id();
            $table->integer('tso_id');
            $table->integer('shop_id');
            $table->integer('rack_id');
            $table->string('remarks');
            $table->tinyInteger('assign_status')->comment('1 assigned , 0 not assign');
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
        Schema::dropIfExists('assign_racks');
    }
}
