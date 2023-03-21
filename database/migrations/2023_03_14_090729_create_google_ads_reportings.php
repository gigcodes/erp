<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoogleAdsReportings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('google_ads_reportings', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('google_account_id');
            $table->foreign('google_account_id')->references('id')->on('googleadsaccounts')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedBigInteger('google_campaign_id')->nullable();
            $table->string('impression')->nullable();
            $table->string('name')->nullable();
            $table->string('click')->nullable();
            $table->string('cost_micros')->nullable();
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
        Schema::dropIfExists('google_ads_reportings');
    }
}
