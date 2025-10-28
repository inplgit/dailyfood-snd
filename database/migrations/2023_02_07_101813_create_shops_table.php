<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShopsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shops', function (Blueprint $table) {
            $table->id();
            $table->string('shop_code');
            $table->string('custome_code')->nullable();
            $table->string('company_name')->nullable();
            $table->string('title')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('email')->nullable();
            $table->string('alt_email')->nullable();
            $table->string('phone')->nullable();
            $table->string('mobile_no')->nullable();
            $table->string('alt_phone')->nullable();
            $table->longtext('address')->nullable();
            $table->longtext('address_2')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip_code')->nullable();
            $table->longtext('note')->nullable();
            $table->boolean('filer')->nullable()->comment('0= Non filer 1 =filer');
            $table->string('cnic')->nullable();
            $table->integer('allow_credit_days')->default()->nullable();
            $table->decimal('allow_credit_amount',12,2)->nullable();
            $table->integer('delvery_days')->default(1)->nullable();
            $table->integer('shop_type_id')->nullable();
            $table->integer('shop_zone_id')->nullable();
            $table->decimal('invoice_discount',15,2)->nullable();
            $table->string('latitude')->nullable()->nullable();
            $table->string('longitude')->nullable();
            $table->decimal('location_radius',15,2)->nullable();
            $table->integer('distributor_id')->nullable();
            $table->integer('tso_id')->nullable();
            $table->integer('route_id')->nullable();
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
        Schema::dropIfExists('shops');
    }
}
