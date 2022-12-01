<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class AddColumnChatMessagesTableUserFeedback extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('chat_messages', function ($table) {
            if (! Schema::hasColumn('chat_messages', 'user_feedback_id')) {
                $table->integer('user_feedback_id')->nullable();
            }
            if (! Schema::hasColumn('chat_messages', 'user_feedback_category_id')) {
                $table->integer('user_feedback_category_id')->nullable();
            }
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
