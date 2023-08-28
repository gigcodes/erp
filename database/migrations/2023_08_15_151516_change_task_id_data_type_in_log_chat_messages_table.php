<?php

use App\LogChatMessage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeTaskIdDataTypeInLogChatMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('log_chat_messages', function (Blueprint $table) {
            $table->integer('task_id')->change();
        });

        // Update if task_id = undefined to null
        LogChatMessage::where('task_id', 'undefined')->update(['task_id' => null]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('log_chat_messages', function (Blueprint $table) {
            //
        });
    }
}
