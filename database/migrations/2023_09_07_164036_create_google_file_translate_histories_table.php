<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGoogleFileTranslateHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('google_file_translate_histories', function (Blueprint $table) {
            $table->id();
            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();
            $table->integer('approved_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('status')->nullable();
            $table->integer('google_file_translate_csv_data_id')->nullable();
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
        Schema::dropIfExists('google_file_translate_histories');
    }
}
