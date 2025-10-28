<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddcolumnIntoStocks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::table('stocks', function (Blueprint $table) {
           $table->decimal('rate',12,2)->default(0)->after('qty');
           $table->decimal('discount_amount',12,2)->default(0)->after('rate');
           $table->decimal('tax_amount',12,2)->default(0)->after('discount_amount');
       });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      //  Schema::dropIfExists('stocks');
    }
}
