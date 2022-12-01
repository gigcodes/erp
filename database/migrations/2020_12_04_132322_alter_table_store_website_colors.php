<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableStoreWebsiteColors extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('store_website_colors', function (Blueprint $table) {
            $table->integer('platform_id')->nullable()->after('store_color');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('store_website_colors', function (Blueprint $table) {
            $table->dropField('platform_id');
        });
    }
}
