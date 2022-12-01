<?php

use Illuminate\Database\Migrations\Migration;

class AlterTableChatMessageAddIndexCharityId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        \DB::statement('ALTER TABLE `chat_messages` ADD INDEX(`charity_id`);');
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
