<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStoreWebsiteCategoryUserHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

    public function up()
    {
        if (!Schema::hasTable('store_website_category_user_history')) {
            Schema::create('store_website_category_user_history', function (Blueprint $table) {
                $table->increments('id');
                $table->String('store_id');
                $table->String('category_id');
                $table->String('user_id');
                $table->String('user_name');
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
