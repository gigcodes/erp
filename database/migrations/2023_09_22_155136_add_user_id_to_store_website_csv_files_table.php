<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUserIdToStoreWebsiteCsvFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('store_website_csv_files', function (Blueprint $table) {
            $table->integer('user_id');
            $table->text('command');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('store_website_csv_files', function (Blueprint $table) {
            $table->dropColumn('user_id');
            $table->dropColumn('command');
        });
    }
}
