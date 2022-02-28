<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChatbotTypeErrorLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chatbot_type_error_logs', function (Blueprint $table) {
            $table->increments('id');
			$table->integer('store_website_id')->index()->nullabel();
            $table->integer('chatbot_id')->index()->nullabel();
			$table->string('phone_number')->nullable();
            $table->string('type_error')->nullable();
			$table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chatbot_Error_Logs');
    }
}
