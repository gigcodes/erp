<?php

use Illuminate\Database\Migrations\Migration;

class AlterWatsonChatJourneyTable1 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::statement('ALTER TABLE watson_chat_journey MODIFY COLUMN chat_id TEXT');
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
