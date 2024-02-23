<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewColumnsInZoomMeetingParticipantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('zoom_meeting_participants', function (Blueprint $table) {
            $table->dateTime('join_time')->nullable();
            $table->dateTime('leave_time')->nullable();
            $table->integer('duration');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('zoom_meeting_participants', function (Blueprint $table) {
            $table->dropColumn('join_time');
            $table->dropColumn('leave_time');
            $table->dropColumn('duration');
        });
    }
}
