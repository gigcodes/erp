<?php

use Illuminate\Database\Migrations\Migration;

class AlterFlowActionMessagesHtmlContentNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::statement('ALTER TABLE `flow_action_messages` CHANGE `html_content` `html_content` TEXT NULL ');
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
