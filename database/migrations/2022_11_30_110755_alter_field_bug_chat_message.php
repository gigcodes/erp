<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterFieldBugChatMessage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
//        Schema::table('chat_messages', function (Blueprint $table) {
//            $table->bigInteger('bug_id')->nullable()->after('developer_task_id');
//
//        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
//        Schema::table('chat_messages', function (Blueprint $table) {
//            $table->dropColumn('bug_id');
//        });
    }
}
