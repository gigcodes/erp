<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDubbizleIdToMessages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->integer('dubbizle_id')->unsigned()->nullable()->after('erp_user');

            // $table->foreign('dubbizle_id')->references('id')->on('dubbizles');
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
            // $table->dropForeign(['dubbizle_id']);

            $table->dropColumn('dubbizle_id');
        });
    }
}
