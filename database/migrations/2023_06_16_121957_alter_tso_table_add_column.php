<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTsoTableAddColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tso', function (Blueprint $table) {
            $table->date('date_of_join')->nullable();
            $table->date('date_of_leaving')->nullable();
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
        $table->dropColumn(['date_of_join','date_of_leaving']);
        });
    }
}
