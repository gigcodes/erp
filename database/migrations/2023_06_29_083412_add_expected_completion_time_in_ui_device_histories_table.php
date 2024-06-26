<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddExpectedCompletionTimeInUiDeviceHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ui_device_histories', function (Blueprint $table) {
            $table->timestamp('expected_completion_time')->after('estimated_time')->nullable();
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
            $table->dropColumn('expected_completion_time');
        });
    }
}
