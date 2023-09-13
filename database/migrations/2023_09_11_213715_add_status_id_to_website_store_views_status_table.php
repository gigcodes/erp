<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusIdToWebsiteStoreViewsStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('website_store_views_status', function (Blueprint $table) {
            $table->text('website_store_views_status_status_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('website_store_views_status', function (Blueprint $table) {
            $table->text('website_store_views_status_status_id');
        });
    }
}
