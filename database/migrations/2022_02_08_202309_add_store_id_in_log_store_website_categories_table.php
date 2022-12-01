<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStoreIdInLogStoreWebsiteCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('log_store_website_categories', function (Blueprint $table) {
            $table->integer('store_id')->after('category_id')->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('log_store_website_categories', function (Blueprint $table) {
            $table->dropColumn(['store_id']);
        });
    }
}
