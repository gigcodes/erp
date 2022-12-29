<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddKeyChatbotDialogTableStoreWebsite extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        DB::statement('ALTER TABLE `chatbot_dialog_responses` CHANGE `store_website_id` `store_website_id` INT(11) NULL DEFAULT NULL;');
		DB::statement('update chatbot_dialog_responses set store_website_id= NULL where store_website_id =0;');
		DB::statement('ALTER TABLE `chatbot_dialog_responses` ADD FOREIGN KEY (`store_website_id`) REFERENCES `store_websites`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;');
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
