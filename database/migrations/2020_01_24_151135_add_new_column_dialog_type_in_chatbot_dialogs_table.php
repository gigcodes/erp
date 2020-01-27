<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewColumnDialogTypeInChatbotDialogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chatbot_dailogs', function (Blueprint $table) {
            $table->enum('dialog_type',['node', 'folder']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('chatbot_dailogs', function (Blueprint $table) {
            $table->dropColumn('dialog_type');
        });
    }
}
