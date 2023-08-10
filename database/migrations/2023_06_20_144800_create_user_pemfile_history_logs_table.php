<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserPemfileHistoryLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_pemfile_history_logs', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('user_pemfile_history_id');
            $table->text('cmd');
            $table->json('output')->nullable();
            $table->integer('return_var')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_pemfile_history_logs');
    }
}
