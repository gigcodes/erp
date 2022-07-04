<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersFeedbackHrTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_feedback_hr_tickets', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('feedback_cat_id')->nulable();
            $table->integer('user_id')->nulable();
            $table->longText('task_subject')->nulable();
            $table->longText('task_type')->nulable();
            $table->bigInteger('repository_id')->nulable();
            $table->longText('task_detail')->nulable();
            $table->string('cost')->nulable();
            $table->bigInteger('task_asssigned_to')->nulable();
            $table->string('status')->default('In progress');
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
        Schema::dropIfExists('users_feedback_hr_tickets');
    }
}
