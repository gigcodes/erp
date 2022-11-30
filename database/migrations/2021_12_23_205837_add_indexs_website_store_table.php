<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexsWebsiteStoreTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('website_stores', function (Blueprint $table) {
            $table->index(['website_id']);
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
        Schema::create('website_stores', function (Blueprint $table) {
            $table->dropIndex(['website_id']);
            $table->dropIndex(['platform_id']);
        });
    }
}
