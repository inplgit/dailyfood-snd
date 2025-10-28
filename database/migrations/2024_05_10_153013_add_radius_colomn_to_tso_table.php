<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRadiusColomnToTsoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tso', function (Blueprint $table) {
            $table->string('location_name')->nullable()->after('geography_id');
            $table->decimal('radius' , 15 , 2)->nullable()->after('latitude');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tso', function (Blueprint $table) {
            $table->string('location_name')->nullable()->after('geography_id');
            $table->decimal('radius' , 15 , 2)->nullable()->after('latitude');
        });
    }
}
