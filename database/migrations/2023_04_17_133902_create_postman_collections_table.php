<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostmanCollectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('postman_collections', function (Blueprint $table) {
            $table->increments('id');
            $table->string('collection_id')->nullable();
            $table->string('workspace_id')->nullable();
            $table->string('collection_name')->nullable();
            $table->string('workspace_name')->nullable();
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
        Schema::dropIfExists('assets_manager_link_user');
    }
};