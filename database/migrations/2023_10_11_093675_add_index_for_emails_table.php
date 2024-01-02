<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexForEmailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->index('email_id');
        });

        Schema::table('email_category', function (Blueprint $table) {
            $table->index('priority');
        });

        Schema::table('emails', function (Blueprint $table) {
            $table->index('seen');
            $table->index('is_draft');
        });

        Schema::table('email_assignes', function (Blueprint $table) {
            $table->index('user_id');
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
            $table->dropIndex('email_id');
        });

        Schema::table('email_category', function (Blueprint $table) {
            $table->dropIndex('priority');
        });

        Schema::table('emails', function (Blueprint $table) {
            $table->dropIndex('seen');
            $table->dropIndex('is_draft');
        });

        Schema::table('email_assignes', function (Blueprint $table) {
            $table->dropIndex('user_id');
        });
    }
}
