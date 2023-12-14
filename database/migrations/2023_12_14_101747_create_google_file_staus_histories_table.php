<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoogleFileStausHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('google_file_staus_histories', function (Blueprint $table) {
            $table->id();
            $table->integer('google_file_translate_id')->default(0);
            $table->integer('updated_by_user_id')->default(0);
            $table->integer('old_status')->default(0);
            $table->integer('status')->default(0);
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
        Schema::dropIfExists('google_file_staus_histories');
    }
}
