<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMasterToVendorFlowChartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vendor_flow_charts', function (Blueprint $table) {
            $table->bigInteger('master_id')->unsigned()->nullable()->after('id');
            $table->foreign('master_id')->references('id')->on('vendor_flow_chart_masters')->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vendor_flow_charts', function (Blueprint $table) {
            // 1. Drop foreign key constraints
            $table->dropForeign(['vendor_flow_charts_master_id_foreign']);

            // 2. Drop the column
            $table->dropColumn('master_id');
        });
    }
}
