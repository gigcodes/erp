<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sentry_error_logs', function (Blueprint $table) {
            $table->string('total_events')->nullable()->after('project_id');
            $table->string('total_user')->nullable()->after('total_events');
            $table->string('device_name')->nullable()->after('total_user');
            $table->string('os')->nullable()->after('device_name');
            $table->string('os_name')->nullable()->after('os');
            $table->string('release_version')->nullable()->after('os_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sentry_error_logs', function (Blueprint $table) {
            
        });
    }
};
