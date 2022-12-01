<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterStoreWebsitePagesAddIsTranslated extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('store_website_pages', function (Blueprint $table) {
            $table->boolean('is_latest_version_translated')->default('0')->after('meta_keyword_avg_monthly');
            $table->boolean('is_latest_version_pushed')->default('0')->after('is_pushed');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
