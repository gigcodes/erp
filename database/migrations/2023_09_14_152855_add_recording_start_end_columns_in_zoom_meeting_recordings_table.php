<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRecordingStartEndColumnsInZoomMeetingRecordingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('zoom_meeting_recordings', function (Blueprint $table) {
            $table->dateTime('recording_start');
            $table->dateTime('recording_end');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('zoom_meeting_recordings', function (Blueprint $table) {
            $table->dropColumn('recording_start');
            $table->dropColumn('recording_end');
        });
    }
}
