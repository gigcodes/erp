<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogStoreWebsiteCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //if (!Schema::hasTable('log_store_website_categories')) {
        Schema::create('log_store_website_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->String('log_case_id');
            $table->String('category_id');
            $table->String('log_detail');
            $table->String('log_msg');
            $table->String('description');
            $table->timestamps();
        });
        // }
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
