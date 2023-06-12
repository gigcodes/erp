<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTmpRepliesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tmp_replies', function (Blueprint $table) {
            $table->id();
            $table->integer("chat_message_id");
            $table->string("suggested_replay")->nullable();
            $table->boolean("is_approved")->default(false)->nullable();
            $table->boolean("is_reject")->default(false)->nullable();
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tmp_replies');
    }
}
