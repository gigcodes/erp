<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableStoreWebsitePageHistoryResponseUrl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table("store_website_page_histories",function(Blueprint $table) {
            $table->string("url")->after("content")->index(); 
            $table->text("result")->after("url"); 
            $table->string("result_type")->after("result")->index(); 
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
        Schema::table("store_website_page_histories",function(Blueprint $table) {
            $table->dropField("url"); 
            $table->dropField("result");
            $table->dropField("result_type"); 
        });
    }
}
