<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddApprovalToTaskDueDateHistoryLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('task_due_date_history_logs', function (Blueprint $table) {
            \DB::statement('ALTER TABLE `task_due_date_history_logs` ADD `approved` TINYINT NOT NULL AFTER `new_due_date`, ADD INDEX (`approved`);');
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
            $table->dropColumn('approved');
        });
    }
}
