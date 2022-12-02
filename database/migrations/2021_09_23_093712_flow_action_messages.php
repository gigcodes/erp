<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FlowActionMessages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('flow_action_messages', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('action_id');
            $table->string('sender_name');
            $table->string('sender_email_address');
            $table->string('subject');
            $table->text('html_content');
            $table->string('reply_to_email');
            $table->boolean('sender_email_as_reply_to')->default(1);
            $table->text('deleted')->nullable();
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
        Schema::dropIfExists('flow_messages');
    }
}
