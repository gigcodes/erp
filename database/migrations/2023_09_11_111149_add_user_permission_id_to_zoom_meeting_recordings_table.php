<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUserPermissionIdToZoomMeetingRecordingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('zoom_meeting_recordings', function (Blueprint $table) {
            $table->text('user_record_permission')->nullable();
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
            $table->dropColumn('user_record_permission');
        });
    }
}
