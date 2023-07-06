<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExpectedStartTimeInUiDevicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ui_devices', function (Blueprint $table) {
            $table->timestamp('expected_start_time')->after('estimated_time')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ui_devices', function (Blueprint $table) {
            $table->dropColumn('expected_start_time');
        });
    }
}
