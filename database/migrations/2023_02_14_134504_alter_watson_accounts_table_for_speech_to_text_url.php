<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterWatsonAccountsTableForSpeechToTextUrl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('watson_accounts', function (Blueprint $table) {
            $table->string('speech_to_text_url')->after('url');
            $table->string('speech_to_text_api_key')->after('url');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('watson_accounts', function(Blueprint $table) {
            $table->dropColumn('speech_to_text_url');
            $table->dropColumn('speech_to_text_api_key');
        });
    }
}
