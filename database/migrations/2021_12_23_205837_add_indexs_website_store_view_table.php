<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexsWebsiteStoreViewTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('website_store_views', function (Blueprint $table) {
            $table->index(['code']);
            $table->index(['website_store_id']);
            $table->index(['platform_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('website_store_views', function (Blueprint $table) {
            $table->dropIndex(['website_store_id']);
            $table->dropIndex(['platform_id']);
            $table->dropIndex(['code']);
        });
    }
}
