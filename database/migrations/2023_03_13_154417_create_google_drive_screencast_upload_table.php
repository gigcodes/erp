<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoogleDriveScreencastUploadTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('google_drive_screencast_upload', function (Blueprint $table) {
            $table->increments('id');
            $table->string('file_name', 500)->nullable();
            $table->string('extension');
            $table->string('google_drive_file_id', 1000);
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
        Schema::dropIfExists('google_drive_screencast_upload');
    }
}
