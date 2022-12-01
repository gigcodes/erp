<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWatsonChatJourneyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('watson_chat_journey', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('chat_id')->nullable();
            $table->boolean('chat_entered')->default(0);
            $table->text('message_received')->nullable();
            $table->boolean('reply_found_in_database')->default(0);
            $table->boolean('reply_searched_in_watson')->default(0);
            $table->text('reply')->nullable();
            $table->boolean('response_sent_to_cusomer')->default(0);
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
        Schema::dropIfExists('watson_chat_journey');
    }
}
