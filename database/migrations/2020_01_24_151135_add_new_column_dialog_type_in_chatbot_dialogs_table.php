<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewColumnDialogTypeInChatbotDialogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('chatbot_dialogs', function (Blueprint $table) {
            $table->enum('dialog_type', ['node', 'folder']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('chatbot_dialogs', function (Blueprint $table) {
            $table->dropColumn('dialog_type');
        });
    }
}
