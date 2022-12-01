<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableSuggestedProductExtraFieldAttach extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('suggested_products', function (Blueprint $table) {
            $table->string('platform')->nullable()->default('attachment')->after('chat_message_id');
            $table->integer('platform_id')->nullable()->after('platform');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('suggested_products', function (Blueprint $table) {
            $table->dropField('platform');
            $table->dropField('platform_id');
        });
    }
}
