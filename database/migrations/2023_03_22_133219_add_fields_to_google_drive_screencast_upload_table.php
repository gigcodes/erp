<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToGoogleDriveScreencastUploadTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('google_drive_screencast_upload', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->after('extension');
            $table->unsignedBigInteger('developer_task_id')->nullable()->after('user_id');
            $table->text('read')->nullable()->after('user_id');
            $table->text('write')->nullable()->after('read');
            $table->text('remarks')->nullable()->after('write');
            $table->date('file_creation_date')->nullable()->after('remarks');
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
            $table->dropColumn('user_id','developer_task_id','read','write','remarks','file_creation_date');
        });
    }
}
