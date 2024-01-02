<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsEstimatedTimeApprovedInUiDeviceHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ui_device_histories', function (Blueprint $table) {
            $table->boolean('is_estimated_time_approved')->nullable()->after('expected_completion_time');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ui_device_histories', function (Blueprint $table) {
            $table->dropColumn('is_estimated_time_approved');
        });
    }
}
