<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterLogChatMessagesTaskIdNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::table('log_chat_messages', function (Blueprint $table) {
        // $table->integer('task_id')->unsigned()->nullable(true)->change();
        DB::statement('ALTER TABLE `log_chat_messages` MODIFY `task_id` INTEGER UNSIGNED NULL;');
    //    });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
