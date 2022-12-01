<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterChatMessagesTableAddField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->string('from_email')->nullable()->after('is_email');
            $table->string('to_email')->nullable()->after('from_email');
            $table->string('cc_email')->nullable()->after('to_email');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('scrapers', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}
