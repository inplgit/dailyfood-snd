<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTSOTargetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tso_targets', function (Blueprint $table) {
            $table->foreignId('tso_id')->constrained('tso');
            $table->foreignId('product_id')->constrained('products');
            $table->date('month');
            $table->decimal('qty', 8, 2);
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
        Schema::dropIfExists('tso_targets');
    }
}
