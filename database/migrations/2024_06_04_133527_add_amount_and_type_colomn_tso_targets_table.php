<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAmountAndTypeColomnTsoTargetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tso_targets', function (Blueprint $table) {
            $table->tinyInteger('type')->nullable()->after('month');
            $table->decimal('qty' , 10 , 2)->nullable()->change();
            $table->decimal('amount' , 10 , 2)->nullable()->after('qty');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tso_targets', function (Blueprint $table) {
            $table->tinyInteger('type')->nullable()->after('month');
            $table->decimal('qty' , 10 , 2)->nullable()->change();
            $table->decimal('amount' , 10 , 2)->nullable()->after('qty');
        });
    }
}
