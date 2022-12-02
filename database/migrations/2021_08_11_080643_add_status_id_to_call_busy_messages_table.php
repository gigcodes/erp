<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusIdToCallBusyMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('call_busy_messages', function (Blueprint $table) {
            $table->integer('call_busy_message_statuses_id')->index()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('call_busy_messages', function (Blueprint $table) {
            $table->dropIndex(['call_busy_message_statuses_id']);
            $table->dropColumn('call_busy_message_statuses_id');
        });
    }
}
