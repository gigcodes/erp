<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableSocialAds extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('social_ads', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('config_id');
            $table->string('adset_id');
            $table->string('name');
            $table->string('creative_id')->nullable();
            $table->string('ad_set_name')->nullable();
            $table->string('ad_creative_name')->nullable();
            $table->string('status')->nullable();
            $table->string('live_status')->nullable();
            $table->string('ref_ads_id')->nullable();
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
        Schema::dropIfExists('social_ads');
    }
}
