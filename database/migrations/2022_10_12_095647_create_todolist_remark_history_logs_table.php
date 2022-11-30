<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTodolistRemarkHistoryLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('todolist_remark_history_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('todo_list_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->text('remark')->nullable();
            $table->text('old_remark')->nullable();
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
        Schema::dropIfExists('todolist_remark_history_logs');
    }
}
