<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRouteRadiusConfigurationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('route_radius_configurations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('distributor_id');
            $table->unsignedBigInteger('tso_id');
            $table->decimal('radius', 8, 2);
            $table->unsignedBigInteger('created_by')->nullable();
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
        Schema::dropIfExists('route_radius_configurations');
    }
}
