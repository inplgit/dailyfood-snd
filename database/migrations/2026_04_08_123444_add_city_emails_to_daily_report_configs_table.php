<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCityEmailsToDailyReportConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('daily_report_configs', function (Blueprint $table) {
            $table->text('city_emails')->nullable()->after('cc_emails');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('daily_report_configs', function (Blueprint $table) {
            $table->dropColumn('city_emails');
        });
    }
}
