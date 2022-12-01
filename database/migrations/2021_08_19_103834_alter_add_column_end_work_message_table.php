<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterAddColumnEndWorkMessageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('store_website_twilio_numbers', function (Blueprint $table) {
            $table->text('end_work_message')->nullable();
        });

        DB::select('ALTER TABLE `store_website_twilio_numbers` CHANGE `message_available` `message_available` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;');
        DB::select('ALTER TABLE `store_website_twilio_numbers` CHANGE `message_not_available` `message_not_available` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;');
        DB::select('ALTER TABLE `store_website_twilio_numbers` CHANGE `message_busy` `message_busy` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;');
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
