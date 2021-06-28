<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexToChatMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            // $table->index(['message','number','approved','status']);
            $table->index(['message']);
            $table->index(['number']);
            $table->index(['approved']);
            $table->index(['status']);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->dropIndex(['message']);
            $table->dropIndex(['number']);
            $table->dropIndex(['approved']);
            $table->dropIndex(['status']);

        });
    }
}
