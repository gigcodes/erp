<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableInfluencerKeyword extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('influencer_keywords',function(Blueprint $table) {
            $table->string("wait_time")->nullable()->after("instagram_account_id");
            $table->string("no_of_requets")->nullable()->after("wait_time");
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
        Schema::table('influencer_keywords',function(Blueprint $table) {
            $table->dropField("wait_time");
            $table->dropField("no_of_requets");
        }); 
    }
}
