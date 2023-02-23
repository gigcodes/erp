<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoogleResponsiveDisplayAdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('google_responsive_display_ads', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('adgroup_google_campaign_id')->nullable();
            $table->unsignedBigInteger('google_adgroup_id')->nullable();
            $table->unsignedBigInteger('google_ad_id')->nullable();
            $table->string('headline1')->nullable();
            $table->string('headline2')->nullable();
            $table->string('headline3')->nullable();
            $table->string('description1')->nullable();
            $table->string('description2')->nullable();
            $table->text('long_headline')->nullable();
            $table->string('business_name')->nullable();
            $table->text('final_url')->nullable();
            $table->text('ads_response')->nullable();
            $table->string('status')->nullable()->comment('E.g UNKNOWN, ENABLED, PAUSED, REMOVED');
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
        Schema::dropIfExists('google_responsive_display_ads');
    }
}
