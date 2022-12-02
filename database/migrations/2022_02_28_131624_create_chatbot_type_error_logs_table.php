<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->integer('store_website_id')->index()->nullable();
            $table->string('call_sid')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('type_error')->nullable();
            $table->boolean('is_active')->default(1);
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
