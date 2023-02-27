<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCommandToTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->string('command')->after('parent_review_task_id')->nullable();
            $table->string('parameters')->after('command')->nullable();
            $table->string('timezone')->after('parameters')->nullable();
            $table->string('expression')->after('timezone')->nullable();
            $table->string('notification_email_address')->after('expression')->nullable();
            $table->string('notification_phone_number')->after('notification_email_address')->nullable();
            $table->string('notification_slack_webhook')->after('notification_phone_number')->nullable();
            $table->string('dont_overlap')->after('notification_slack_webhook')->nullable();
            $table->string('run_in_maintenance')->after('dont_overlap')->nullable();
            $table->string('run_on_one_server')->after('run_in_maintenance')->nullable();
            $table->integer('auto_cleanup_num')->after('run_on_one_server')->nullable();
            $table->string('auto_cleanup_type')->after('auto_cleanup_num')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tasks', function (Blueprint $table) {
            //
        });
    }
}
