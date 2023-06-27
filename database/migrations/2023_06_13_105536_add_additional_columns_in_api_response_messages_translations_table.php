<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAdditionalColumnsInApiResponseMessagesTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('api_response_messages_translations', function (Blueprint $table) {
            $table->integer('api_response_message_id');
            $table->string('translate_from');
            $table->string('translate_to');
            $table->text('translate_text');
            $table->boolean('status')->nullable();
            $table->integer('updated_by_user_id')->nullable();
            $table->integer('approved_by_user_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('api_response_messages_translations', function (Blueprint $table) {
            $table->dropColumn('api_response_message_id');
            $table->dropColumn('translate_from');
            $table->dropColumn('translate_to');
            $table->dropColumn('translate_text');
            $table->dropColumn('status');
            $table->dropColumn('updated_by_user_id');
            $table->dropColumn('approved_by_user_id');
        });
    }
}
