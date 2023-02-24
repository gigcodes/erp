<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTimeDoctorActivitySummariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('time_doctor_activity_summaries', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->date('date');
            $table->integer('tracked')->default(0);
            $table->integer('user_requested');
            $table->integer('accepted')->default(0);
            $table->integer('rejected')->default(0);
            $table->text('rejection_note')->nullable();
            $table->integer('sender');
            $table->integer('receiver');
            $table->string('forworded_person');
            $table->longText('approved_ids')->nullable();
            $table->longText('rejected_ids')->nullable();
            $table->boolean('final_approval')->default(0);
            $table->integer('pending')->nullable();
            $table->longText('pending_ids')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('time_doctor_activity_summaries');
    }
}
