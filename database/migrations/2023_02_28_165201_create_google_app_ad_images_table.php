<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoogleAppAdImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('google_app_ad_images', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('google_app_ad_id')->nullable()->comment('google_app_ads table id');
            $table->unsignedBigInteger('google_asset_id')->nullable();
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
        Schema::dropIfExists('google_app_ad_images');
    }
}
