<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterStoreWebsiteTwilioNumbersAddMessages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('store_website_twilio_numbers', function (Blueprint $table) {
            $table->string('greeting_message')->nullable();
            $table->string('category_menu_message')->nullable();
            $table->string('sub_category_menu_message')->nullable();
            $table->string('speech_response_not_available_message')->nullable();
        });
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
