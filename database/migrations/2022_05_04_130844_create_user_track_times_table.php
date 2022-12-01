<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserTrackTimesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_track_times', function (Blueprint $table) {
            $table->increments('id');
            $table->string('user_id')->nullable();
            $table->string('user_name')->nullable();
            $table->string('hubstaff_tracked_hours')->nullable();
            $table->string('hours_tracked_with')->nullable();
            $table->string('hours_tracked_without')->nullable();
            $table->string('task_id')->nullable();
            $table->string('approved_hours')->nullable();
            $table->string('difference_hours')->nullable();
            $table->string('total_hours')->nullable();
            $table->string('activity_levels')->nullable();
            $table->char('status', 5)->default(1);
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
        Schema::dropIfExists('user_track_times');
    }
}
