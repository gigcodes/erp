<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCampaignAdgroupIdFieldInGoogleAdsReportingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('google_ads_reportings', function (Blueprint $table) {
            $table->unsignedBigInteger('google_customer_id')->nullable()->after('id');
            $table->unsignedBigInteger('adgroup_google_campaign_id')->nullable()->after('google_customer_id');
            $table->unsignedBigInteger('google_adgroup_id')->nullable()->after('adgroup_google_campaign_id');
            $table->unsignedBigInteger('google_ad_id')->nullable()->after('google_adgroup_id');


            $table->string('campaign_type')->nullable()->after('google_account_id');
            $table->string('average_cpc')->nullable()->after('cost_micros');
            $table->date('date')->nullable()->after('average_cpc');

            $table->dropColumn('google_campaign_id');
            $table->dropColumn('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('google_ads_reportings', function (Blueprint $table) {
            //
        });
    }
}
