<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCampaignIdInGoogleAppAdImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('google_app_ad_images', function (Blueprint $table) {
            $table->unsignedInteger('google_customer_id')->nullable()->after('id');
            $table->unsignedBigInteger('adgroup_google_campaign_id')->nullable()->after('google_customer_id');
            $table->unsignedBigInteger('google_adgroup_id')->nullable()->after('adgroup_google_campaign_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('google_app_ad_images', function (Blueprint $table) {
            //
        });
    }
}
