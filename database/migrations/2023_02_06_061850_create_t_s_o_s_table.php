<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTSOSTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tso', function (Blueprint $table) {
            $table->id();
            $table->string('tso_code')->unique();
            $table->string('name');
            $table->string('emp_id')->unique();            
            $table->string('phone')->unique();
            $table->string('cell_phone')->nullable();
            $table->string('alt_phone')->nullable();
            $table->string('address')->nullable();
            $table->string('city');
            $table->string('state');
            $table->string('country');
            $table->string('zip_code')->nullable();
            $table->text('notes')->nullable();          
            $table->foreignId('user_id')->constrained('users')->comment('belongs to user model');
            $table->unsignedBigInteger('distributor_id')->comment('belongs to dritributor model');
            $table->unsignedBigInteger('manager');
            $table->unsignedBigInteger('kpo');
            $table->unsignedBigInteger('kpo_2');
            $table->unsignedBigInteger('kpo_3');
            $table->unsignedBigInteger('department_id')->comment('belongs to department model');
            $table->unsignedBigInteger('designation_id')->comment('belongs to designation model');
            $table->unsignedInteger('spot_sale')->comment('0=No 1=Yes');
            $table->unsignedInteger('auto_payment')->comment('0=No 1=Yes');
            $table->unsignedBigInteger('geography_id');
            $table->string('image_path')->nullable();
            $table->smallInteger('status')->default(1)->comment('0=ACTIVE 1=NOT ACTIVE');
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
        Schema::dropIfExists('tso');
    }
}
