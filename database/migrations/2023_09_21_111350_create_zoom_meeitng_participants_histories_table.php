<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateZoomMeeitngParticipantsHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zoom_meeitng_participants_histories', function (Blueprint $table) {
            $table->id();
            $table->integer('zoom_meeting_participant_id');
            $table->integer('user_id');
            $table->text('type');
            $table->text('oldvalue')->nullable();
            $table->text('newvalue')->nullable();
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
        Schema::dropIfExists('zoom_meeitng_participants_histories');
    }
}
