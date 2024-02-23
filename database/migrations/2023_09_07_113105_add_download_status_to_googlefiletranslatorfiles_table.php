<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDownloadStatusToGooglefiletranslatorfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('googlefiletranslatorfiles', function (Blueprint $table) {
            $table->boolean('download_status')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('googlefiletranslatorfiles', function (Blueprint $table) {
            $table->dropColumn('download_status');
        });
    }
}
