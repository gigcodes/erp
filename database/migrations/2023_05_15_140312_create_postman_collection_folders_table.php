<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostmanCollectionFoldersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('postman_collection_folders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('postman_collection_id')->index();
            $table->string('folder_id')->nullable();
            $table->string('folder_name')->nullable();
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
        Schema::dropIfExists('postman_collection_folders');
    }
}
