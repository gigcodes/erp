<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddABelongsableColumnOnGoogleDriveScreencastUploadTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('google_drive_screencast_upload', function (Blueprint $table) {
            $table->unsignedBigInteger('belongable_id')->nullable();
            $table->string('belongable_type')->nullable();
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
            $table->dropColumn('belongable_id');
            $table->dropColumn('belongable_type');
        });
    }
}
