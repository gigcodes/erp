<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEstTimeToUiDevicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ui_devices', function (Blueprint $table) {
            $table->time('estimated_time')->after('status')->nullable();
        });
        Schema::table('ui_device_histories', function (Blueprint $table) {
            $table->time('estimated_time')->after('status')->nullable();
        });
        Schema::table('ui_languages', function (Blueprint $table) {
            $table->time('estimated_time')->after('status')->nullable();
        });
        Schema::table('uicheck_language_message_histories', function (Blueprint $table) {
            $table->time('estimated_time')->after('status')->nullable();
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
            $table->dropColumn('estimated_time');
        });
        Schema::table('ui_device_histories', function (Blueprint $table) {
            $table->dropColumn('estimated_time');
        });
        Schema::table('ui_languages', function (Blueprint $table) {
            $table->dropColumn('estimated_time');
        });
        Schema::table('uicheck_language_message_histories', function (Blueprint $table) {
            $table->dropColumn('estimated_time');
        });
    }
}
