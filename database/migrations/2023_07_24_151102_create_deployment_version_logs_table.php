<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeploymentVersionLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deployment_version_logs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('deployement_version_id');
            $table->text('error_message')->nullable();
            $table->text('build_number')->nullable();
            $table->string('error_code')->nullable();
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
        Schema::dropIfExists('deployment_version_logs');
    }
}
