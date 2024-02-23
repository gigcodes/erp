<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMagentoMulitpleCsvFileIdToStoreWebsiteCsvFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('store_website_csv_files', function (Blueprint $table) {
            $table->text('csv_file_id')->nullable();
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
            $table->dropColumn('csv_file_id');
        });
    }
}
