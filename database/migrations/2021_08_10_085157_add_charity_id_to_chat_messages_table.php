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
       DB::select("ALTER TABLE `customer_charities` ADD `product_id` INT(11) NULL AFTER `category_id`;");
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
