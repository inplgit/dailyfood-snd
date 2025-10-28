<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLongitugeLatitudeColomnToTsoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tso', function (Blueprint $table) {
            $table->string('longitude')->nullable()->after('geography_id');
            $table->string('latitude')->nullable()->after('longitude');
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
            $table->string('longitude')->nullable()->after('geography_id');
            $table->string('latitude')->nullable()->after('longitude');
        });
    }
}
