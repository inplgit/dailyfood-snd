<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UserLocation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::create('users_locations', function (Blueprint $table) {
        $table->increments('id');
        $table->string("latitude");
        $table->string("longitude");
        $table->string("user_id");
        $table->string("table_name");
        $table->integer('table_id');
        $table->string("table_type");
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
        //
    }
}
