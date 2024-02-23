<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateZoomMeetingRecordingsHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zoom_meeting_recordings_histories', function (Blueprint $table) {
            $table->id();
            $table->integer('zoom_meeting_record_id');
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
        Schema::dropIfExists('zoom_meeting_recordings_histories');
    }
}
