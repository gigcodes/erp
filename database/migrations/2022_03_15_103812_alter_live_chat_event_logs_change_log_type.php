<?php

use Illuminate\Database\Migrations\Migration;

class AlterLiveChatEventLogsChangeLogType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::statement('ALTER TABLE live_chat_event_logs MODIFY COLUMN log TEXT');
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
