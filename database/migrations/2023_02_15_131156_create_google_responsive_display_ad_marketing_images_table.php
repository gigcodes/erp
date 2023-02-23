<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoogleResponsiveDisplayAdMarketingImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('google_responsive_display_ad_marketing_images', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('google_responsive_display_ad_id')->nullable()->comment('google_responsive_display_ads table id');
            $table->unsignedBigInteger('google_asset_id')->nullable();
            $table->string('type')->default('NORMAL')->comment('E.g NORMAL, SQUARE');
            $table->text('name')->nullable();
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
        Schema::dropIfExists('google_responsive_display_ad_marketing_images');
    }
}
