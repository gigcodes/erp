<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldToStoreWebsitePagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('store_website_pages', function (Blueprint $table) {
            $table->integer('translated_from')->nullable()->after('copy_page_id');
            $table->integer('is_flagged_translation')->default(0)->after('is_latest_version_pushed');

            $table->integer('approved_by_user_id')->nullable()->after('is_flagged_translation');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('store_website_pages', function (Blueprint $table) {
            $table->dropColumn('is_flagged_translation');
            $table->dropColumn('approved_by_user_id');
        });
    }
}
