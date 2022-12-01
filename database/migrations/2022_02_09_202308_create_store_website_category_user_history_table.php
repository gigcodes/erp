<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoreWebsiteCategoryUserHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('store_website_category_user_history')) {
            Schema::create('store_website_category_user_history', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('store_id');
                $table->integer('category_id');
                $table->integer('user_id');
                $table->String('website_action');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //Schema::dropIfExists('log_scrapers');
    }
}
