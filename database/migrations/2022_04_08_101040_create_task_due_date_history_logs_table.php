<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTaskDueDateHistoryLogsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('task_due_date_history_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('task_id')->nullable();
            $table->string('task_type')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('old_due_date')->nullable();
            $table->integer('new_due_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('task_due_date_history_logs');
    }
}
