<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterScrapperImg34543 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('scraper_imags', function (Blueprint $table) {
            $table->index(['created_at', 'website_id', 'store_website']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('scraper_imags', function (Blueprint $table) {
            $table->dropIndex(['created_at', 'website_id', 'store_website']);
        });
    }
}
