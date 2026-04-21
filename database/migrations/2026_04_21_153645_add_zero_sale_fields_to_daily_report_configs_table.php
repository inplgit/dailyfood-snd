<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddZeroSaleFieldsToDailyReportConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('daily_report_configs', function (Blueprint $table) {
            $table->text('zero_sale_designation_ids')->nullable()->after('designation_ids');
            $table->boolean('show_zero_sale_tso')->default(false)->after('zero_sale_designation_ids');
        });
    }

    public function down()
    {
        Schema::table('daily_report_configs', function (Blueprint $table) {
            $table->dropColumn(['zero_sale_designation_ids', 'show_zero_sale_tso']);
        });
    }
}
