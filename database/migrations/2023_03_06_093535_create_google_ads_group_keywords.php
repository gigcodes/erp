<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoogleAdsGroupKeywords extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('google_ad_group_keywords', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('google_customer_id');
            $table->unsignedBigInteger('adgroup_google_campaign_id');
            $table->unsignedBigInteger('google_adgroup_id');
            $table->unsignedBigInteger('google_keyword_id');
            $table->string('keyword');
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
        Schema::dropIfExists('google_ad_group_keywords');
    }
}
