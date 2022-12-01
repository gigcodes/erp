<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWebsiteStorageIdToProductPushInformationHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_push_information_histories', function (Blueprint $table) {
            $table->unsignedInteger('store_website_id')->index()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_push_information_histories', function (Blueprint $table) {
            $table->dropColumn('store_website_id');
        });
    }
}
