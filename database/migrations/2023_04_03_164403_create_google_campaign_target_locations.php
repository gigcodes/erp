<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGoogleCampaignTargetLocations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('google_campaign_locations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('google_customer_id')->nullable();
            $table->unsignedBigInteger('adgroup_google_campaign_id')->nullable();
            $table->unsignedBigInteger('google_location_id')->nullable();
            $table->string('type')->nullable();
            $table->unsignedBigInteger('country_id')->nullable();
            $table->unsignedBigInteger('state_id')->nullable();
            $table->unsignedBigInteger('city_id')->nullable();
            $table->text('address')->nullable();
            $table->unsignedInteger('distance')->nullable();
            $table->string('radius_units')->nullable();
            $table->boolean('is_target')->default(true);
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
        Schema::dropIfExists('google_campaign_locations');
    }
}
