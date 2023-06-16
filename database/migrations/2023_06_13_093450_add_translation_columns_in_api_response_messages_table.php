<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTranslationColumnsInApiResponseMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('api_response_messages', function (Blueprint $table) {
            $table->boolean('is_flagged')->nullable()->after('value');
            $table->boolean('is_translate')->nullable()->after('is_flagged');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('api_response_messages', function (Blueprint $table) {
            $table->dropColumn("is_flagged");
            $table->dropColumn("is_translate");
        });
    }
}
