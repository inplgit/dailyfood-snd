<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDailyReportConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('daily_report_configs', function (Blueprint $table) {
            $table->id();
            $table->text('emails')->nullable();
            $table->text('city_ids')->nullable();
            $table->boolean('show_tso_attendance')->default(true);
            $table->boolean('show_distributor_sales')->default(true);
            $table->boolean('show_product_sales')->default(true);
            $table->boolean('show_top_bottom_tso')->default(true);
            $table->boolean('show_top_bottom_shop')->default(true);
            $table->boolean('show_overall_sales')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('daily_report_configs');
    }
}
