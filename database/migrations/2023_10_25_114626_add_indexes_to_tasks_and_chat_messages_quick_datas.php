<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexesToTasksAndChatMessagesQuickDatas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->index('is_statutory');
            $table->index('is_verified');
            $table->index('is_completed');
        });

        Schema::table('chat_messages_quick_datas', function (Blueprint $table) {
            $table->index('model');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropIndex('is_statutory');
            $table->dropIndex('is_verified');
            $table->dropIndex('is_completed');
        });

        Schema::table('chat_messages_quick_datas', function (Blueprint $table) {
            $table->dropIndex('model');
        });
    }
}
