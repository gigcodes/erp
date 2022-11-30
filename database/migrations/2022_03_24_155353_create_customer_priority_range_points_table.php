<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerPriorityRangePointsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_priority_range_points', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('store_website_id')->index()->nullable();
            $table->string('twilio_priority_id')->default(0);
            $table->string('min_point')->default(0);
            $table->string('max_point')->default(0);
            $table->string('range_name')->default(0);
            $table->timestamp('deleted_at')->nullable();
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
        Schema::dropIfExists('customer_priority_range_points');
    }
}
