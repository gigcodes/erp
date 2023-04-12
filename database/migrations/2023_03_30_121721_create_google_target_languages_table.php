<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoogleTargetLanguagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('google_campaign_target_languages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('google_customer_id')->nullable();
            $table->unsignedBigInteger('adgroup_google_campaign_id')->nullable();
            $table->unsignedInteger('google_language_constant_id')->nullable();
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
        Schema::dropIfExists('google_campaign_target_languages');
    }
}
