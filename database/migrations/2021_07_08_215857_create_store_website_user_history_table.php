<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStoreWebsiteUserHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('store_website_user_history', function (Blueprint $table) {
            $table->increments('id');
            $table->integer("store_website_id")->nullable();
            $table->integer("store_website_user_id")->nullable();
            $table->string("model")->nullable();
            $table->string("attribute")->nullable();
            $table->string("old_value")->nullable();
            $table->string("new_value")->nullable();
            $table->integer("user_id")->nullable();
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
        Schema::dropIfExists('store_website_user_history');
    }
}
