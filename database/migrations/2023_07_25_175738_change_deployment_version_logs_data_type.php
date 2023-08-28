<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeDeploymentVersionLogsDataType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('deployment_version_logs', function (Blueprint $table) {
            $table->integer('deployement_version_id')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('deployment_version_logs', function (Blueprint $table) {
            $table->bigInteger('deployement_version_id')->change();
        });
    }
}
