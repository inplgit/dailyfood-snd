<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusUserIdColumnsToTso extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tso', function (Blueprint $table) {
            $table->integer('status_user_id')->nullable()->after('status_username');
            $table->string('remarks')->nullable()->after('status_user_id');
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
            $table->dropColumn('status_user_id');
            $table->dropColumn('remarks');
        });
    }
}
