<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnIntoAttendence extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('attendences', function (Blueprint $table) {
            $table->Integer('tso_id')->after('out')->default(0);
            $table->Integer('distribution_id')->after('tso_id')->default(0);
            $table->Integer('route_id')->after('distribution_id')->default(0);
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
