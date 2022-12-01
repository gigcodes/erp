<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableSocialCampaigns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('social_campaigns', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('config_id');
            $table->string('name')->nullable();
            $table->string('objective_name')->nullable();
            $table->string('buying_type')->nullable();
            $table->string('daily_budget')->nullable();
            $table->string('status')->nullable();
            $table->string('ref_campaign_id')->nullable();

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
        Schema::dropIfExists('instagram_logs');
    }
}
