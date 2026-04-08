<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCcEmailsToDailyReportConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('daily_report_configs', function (Blueprint $table) {
            $table->text('cc_emails')->nullable()->after('emails');
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
            $table->dropColumn('cc_emails');
        });
    }
}
