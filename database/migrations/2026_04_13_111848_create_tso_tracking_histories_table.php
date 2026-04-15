<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTsoTrackingHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tso_tracking_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('tso_id')->nullable();
            $table->unsignedBigInteger('distributor_id')->nullable();
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->timestamp('sync_date_time');
            $table->string('local_id')->nullable();
            $table->timestamps();

            // Optional: Indexing for performance
            $table->index(['user_id', 'sync_date_time']);
            $table->index(['tso_id', 'sync_date_time']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tso_tracking_histories');
    }
}
