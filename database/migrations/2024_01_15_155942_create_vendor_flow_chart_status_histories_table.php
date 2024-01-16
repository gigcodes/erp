<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorFlowChartStatusHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vendor_flow_chart_status_histories', function (Blueprint $table) {
            $table->id();
            $table->integer('vendor_id');
            $table->integer('flow_chart_id');
            $table->integer('old_value')->nullable();
            $table->integer('new_value')->nullable();
            $table->integer('user_id');
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
        Schema::dropIfExists('vendor_flow_chart_status_histories');
    }
}
