<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoogleDriveBugsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('google_drive_bugs_upload', function (Blueprint $table) {
            $table->id();
            $table->string("file_name", 500);
            $table->string("extension");
            $table->string("google_drive_file_id", 500);
            $table->date("file_creation_date");
            $table->unsignedBigInteger("bug_id");
            $table->unsignedBigInteger("user_id")->nullable();
            $table->text("remarks")->nullable();
            $table->text("read")->nullable();
            $table->text("write")->nullable();
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
        Schema::dropIfExists('google_drive_bugs_upload');
    }
}
