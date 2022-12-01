<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddApprovalToTaskHistoryForStartDate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('task_history_for_start_date', function (Blueprint $table) {
            \DB::statement('ALTER TABLE `task_history_for_start_date` ADD `approved` TINYINT NOT NULL AFTER `new_value`, ADD INDEX (`approved`);');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('task_history_for_start_date', function (Blueprint $table) {
            $table->dropColumn('approved');
        });
    }
}
