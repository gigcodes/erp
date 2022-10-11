<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateToDoListRemarkHistoryLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('to_do_list_remark_history_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('todo_list_id')->nullble();
            $table->integer('user_id')->nullble();
            $table->text('remark')->nullble();
            $table->text('old_remark')->nullble();
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
        Schema::dropIfExists('to_do_list_remark_history_logs');
    }
}
