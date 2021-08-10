<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddCharityIdToChatMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       DB::select("ALTER TABLE `chat_messages` ADD `charity_id` INT(11) NULL AFTER `vendor_id`;");
       DB::select("ALTER TABLE `chat_messages` ADD INDEX(`user_id`);");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            //
        });
    }
}
