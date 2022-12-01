<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateColumnDatatypeToTaskDueDateHistoryLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('task_due_date_history_logs', function (Blueprint $table) {
            \DB::statement('TRUNCATE TABLE task_due_date_history_logs');
            \DB::statement('ALTER TABLE `task_due_date_history_logs` CHANGE `old_due_date` `old_due_date` DATETIME NULL DEFAULT NULL;');
            \DB::statement('ALTER TABLE `task_due_date_history_logs` CHANGE `new_due_date` `new_due_date` DATETIME NULL DEFAULT NULL;');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('task_due_date_history_logs', function (Blueprint $table) {
        });
    }
}
