<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AltStoreWebsiteIdToStoreWebsiteCategorySeosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('store_website_category_seos', function (Blueprint $table) {
            $table->integer('store_website_id')->nullable()->after('category_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('store_website_category_seos', function (Blueprint $table) {
            $table->dropField('store_website_id');
        });
    }
}
