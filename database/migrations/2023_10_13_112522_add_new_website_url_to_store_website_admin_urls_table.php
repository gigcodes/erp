<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewWebsiteUrlToStoreWebsiteAdminUrlsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('store_website_admin_urls', function (Blueprint $table) {
            $table->string('website_url')->after('store_website_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('store_website_admin_urls', function (Blueprint $table) {
            //
        });
    }
}
