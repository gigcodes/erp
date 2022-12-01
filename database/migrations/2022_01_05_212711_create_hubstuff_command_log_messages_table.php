<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHubstuffCommandLogMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hubstuff_command_log_messages', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('hubstuff_command_log_id')->unsigned()->index()->foreign()->references('id')->on('hubstuff_command_logs')->onDelete('cascade');
            $table->integer('user_id')->nullable();
            $table->string('frequency')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->text('message')->nullable();
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
        Schema::dropIfExists('hubstuff_command_log_messages');
    }
}
