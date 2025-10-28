<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddcolumnIntoDistributor extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('distributors', function (Blueprint $table) {
            $table->string('parent_code')->default(0)->after('distributor_name');
            $table->string('distributor_sub_code')->default(0)->after('parent_code');
            $table->string('level1')->default(0)->after('distributor_sub_code');
            $table->string('level2')->default(0)->after('level1');
            $table->string('level3')->default(0)->after('level2');
        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
       Schema::dropIfExists('shops');
    }
}
