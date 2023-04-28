<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddABugIdColumnInGoogleDriveScreencastUploadTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('google_drive_screencast_upload', function (Blueprint $table) {
            $table->unsignedBigInteger('bug_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('google_drive_screencast_upload', function (Blueprint $table) {
            $table->dropColumn('google_drive_screencast_upload');
        });
    }
}
