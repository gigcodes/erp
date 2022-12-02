<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTaskTimeoutFieldsToTwilioWorkflowTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('twilio_workflows', function (Blueprint $table) {
            $table->integer('task_timeout')->default(0);
            $table->integer('worker_reservation_timeout')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('twilio_workflows', function (Blueprint $table) {
            $table->dropColumn(['task_timeout', 'worker_reservation_timeout']);
        });
    }
}
