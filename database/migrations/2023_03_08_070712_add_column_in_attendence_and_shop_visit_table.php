<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnInAttendenceAndShopVisitTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('attendences', function (Blueprint $table) {
            $table->string('latitude_in')->nullable()->before('created_at');
            $table->string('longitude_in')->nullable()->before('created_at');
            $table->string('latitude_out')->nullable()->before('created_at');
            $table->string('longitude_out')->nullable()->before('created_at');
        });
        Schema::table('shop_visits', function (Blueprint $table) {
            $table->string('latitude')->nullable()->before('created_at');
            $table->string('longitude')->nullable()->before('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('attendence_and_shop_visit', function (Blueprint $table) {
            Schema::table('attendences', function (Blueprint $table) {
                $table->dropColumn(['latitude_in', 'longitude_in', 'latitude_out', 'longitude_out']);
            });
            Schema::table('shop_visits', function (Blueprint $table) {
                $table->dropColumn(['latitude', 'longitude']);
            });
        });
    }
}
