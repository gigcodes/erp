<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSocialAdsHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('social_ads_history', function (Blueprint $table) {
            
            $table->increments('id');
            $table->bigInteger('ad_ac_id')->nullable();
            $table->bigInteger('account_id')->nullable();
            $table->integer('reach')->nullable();
            $table->integer('Impressions')->nullable();
            $table->integer('amount')->nullable();
            $table->integer('cost_p_result')->nullable();

            $table->string('ad_name')->nullable();
            $table->string('status')->nullable();
            $table->string('adset_name')->nullable();
            $table->string('action_type')->nullable();
            $table->string('campaign_name')->nullable();
            $table->longText('thumb_image')->nullable();
            $table->string('end_time')->nullable();


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
        Schema::dropIfExists('social_ads_history');
    }
}
