<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_details', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('user_code')->unique()->nullable(); // Auto-generated User Code
            $table->string('employee_id')->nullable(); // Employee ID
            $table->string('company_name')->nullable(); // Company Name
            $table->string('phone_number')->nullable(); // Phone number
            $table->string('alt_number')->nullable(); // Alternate Number
            $table->string('cnic')->nullable(); // CNIC
            $table->text('address')->nullable(); // Address
            $table->integer('city')->nullable(); // City
            $table->string('state')->nullable(); // State
            $table->string('country')->nullable(); // Country

            // User-related data
            $table->integer('manager')->nullable(); // Manager
            $table->integer('department_id')->nullable(); // Department
            $table->integer('designation_id')->nullable(); // Designation
            $table->integer('geography_id')->nullable(); // Geography
            $table->date('date_of_join')->nullable(); // Date of Joining
            $table->date('date_of_leaving')->nullable(); // Date of Leaving (optional)

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
        Schema::dropIfExists('user_details');
    }
}
