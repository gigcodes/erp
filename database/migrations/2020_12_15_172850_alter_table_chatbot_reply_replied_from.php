<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableChatbotReplyRepliedFrom extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('chatbot_replies', function (Blueprint $table) {
            $table->string('reply_from')->nullable()->index()->after('replied_chat_id');
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
        Schema::table('chatbot_replies', function (Blueprint $table) {
            $table->dropField('reply_from');
        });
    }
}
