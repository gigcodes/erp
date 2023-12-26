<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDevoopsIdToGoogleDriveScreencastUploadTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('google_drive_screencast_upload', function (Blueprint $table) {
            $table->integer('dev_oops_id')->nullable();
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
            //
        });
    }
}
