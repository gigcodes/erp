<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTaskTimeReminderInLogChatMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('log_chat_messages', function (Blueprint $table) {
            $table->integer('task_time_reminder')->default(0)->after('log_msg')->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('log_chat_messages', function (Blueprint $table) {
            $table->dropColumn(['task_time_reminder']);
        });
    }
}
