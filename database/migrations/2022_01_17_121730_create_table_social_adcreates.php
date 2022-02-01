<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableSocialAdCreates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('social_ad_creatives', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('config_id');
            $table->string('name');
            $table->string('object_story_title')->nullable();
            $table->string('object_story_id')->nullable();
            $table->string('live_status')->nullable();
            $table->string('ref_adcreative_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('social_adsets');
    }
}
