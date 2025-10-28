<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShopOutstandingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shops_outstandings', function (Blueprint $table) {
            $table->id();
            $table->integer('shop_id')->default(0);
            $table->decimal('opening',20,2)->default(0);
            $table->decimal('so_amount',20,2)->default(0);
            $table->decimal('sr_amount',20,2)->default(0);
            $table->decimal('rv_amount',20,2)->default(0);
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
        Schema::dropIfExists('shops_outstandings');
    }
}
