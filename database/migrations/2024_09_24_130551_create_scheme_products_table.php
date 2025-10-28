<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchemeProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scheme_product', function (Blueprint $table) {
            $table->id();
            $table->string('scheme_name');
            $table->text('description')->nullable();
            $table->date('date')->nullable();
            $table->tinyInteger('active')->default(1);
            $table->tinyInteger('status')->default(1);
            $table->string('username')->nullable();
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
        Schema::dropIfExists('scheme_product');
    }
}
