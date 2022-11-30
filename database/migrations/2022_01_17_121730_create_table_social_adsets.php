<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableSocialAdsets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('social_adsets', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('config_id');
            $table->integer('campaign_id');
            $table->string('name');
            $table->string('destination_type')->nullable();
            $table->string('billing_event')->nullable();
            $table->string('start_time')->nullable();
            $table->string('end_time')->nullable();
            $table->string('daily_budget')->nullable();
            $table->string('bid_amount')->nullable();
            $table->string('status')->nullable();
            $table->string('live_status')->nullable();
            $table->string('ref_adset_id')->nullable();
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
        Schema::dropIfExists('social_adsets');
    }
}
