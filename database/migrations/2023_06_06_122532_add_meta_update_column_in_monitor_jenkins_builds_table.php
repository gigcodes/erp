<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMetaUpdateColumnInMonitorJenkinsBuildsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('monitor_jenkins_builds', function (Blueprint $table) {
            $table->boolean('meta_update')->nullable()->after('full_log');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('monitor_jenkins_builds', function (Blueprint $table) {
            $table->dropColumn('meta_update');
        });
    }
}
