<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNecessaryChangesInApiReponseMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('api_response_messages', function (Blueprint $table) {
            $table->dropColumn('is_flagged');
            $table->dropColumn('is_translate');
        });

        Schema::table('api_response_messages_translations', function (Blueprint $table) {
            $table->dropColumn('api_response_message_id');
            $table->dropColumn('translate_from');
            $table->dropColumn('translate_to');
            $table->dropColumn('translate_text');
            $table->dropColumn('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('api_reponse_messages', function (Blueprint $table) {
            //
        });
    }
}
