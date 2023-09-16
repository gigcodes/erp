<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoreWebsitePageStatusHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('store_website_page_status_histories', function (Blueprint $table) {
            $table->id();
            $table->integer('old_status_id')->nullable();
            $table->integer('new_status_id')->nullable();
            $table->integer('user_id');
            $table->integer('store_website_page_id');
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
        Schema::dropIfExists('store_website_page_status_histories');
    }
}
