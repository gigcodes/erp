<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWatsonJourneyLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('watson_journey', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('chatbot_message_log_id')->nullable();
            $table->integer('question_id')->nullable();
            $table->boolean('question_created')->default(0);
            $table->boolean('question_example_created')->default(0);
            $table->boolean('question_reply_inserted')->default(0);
            $table->boolean('question_pushed')->default(0);
            $table->boolean('dialog_inserted')->default(0);
            $table->text('request')->nullable();
            $table->text('response')->nullable();

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
        //
    }
}
