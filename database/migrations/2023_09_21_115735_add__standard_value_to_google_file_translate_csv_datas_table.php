<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStandardValueToGoogleFileTranslateCsvDatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('google_file_translate_csv_datas', function (Blueprint $table) {
            $table->text('standard_value')->nullable();
            $table->integer('storewebsite_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('google_file_translate_csv_datas', function (Blueprint $table) {
            $table->dropColumn('standard_value');
            $table->dropColumn('storewebsite_id');
        });
    }
}
