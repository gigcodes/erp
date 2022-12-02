<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorStatusDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vendor_status_details', function (Blueprint $table) {
            $table->increments('id');
            $table->string('status');
            $table->string('hourly_rate');
            $table->string('available_hour');
            $table->string('experience_level');
            $table->string('communication_skill');
            $table->string('agency');
            $table->longText('remark');
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
        Schema::dropIfExists('vendor_status_details');
    }
}
