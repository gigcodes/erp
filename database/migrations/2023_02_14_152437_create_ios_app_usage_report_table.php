<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateIosAppUsageReportTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        Schema::create('ios_usage_report', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('group_by');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('product_id');
            $table->integer('crashes');
            $table->integer('sessions');
            $table->integer('app_store_views');
            $table->integer('unique_app_store_views');
            $table->integer('daily_active_devices');
            $table->integer('monthly_active_devices');
            $table->integer('paying_users');
            $table->integer('impressions');
            $table->integer('uninstalls');
            $table->integer('unique_impressions');
            $table->string('avg_daily_active_devices');
            $table->string('avg_optin_rate');
            $table->string('storefront');
            $table->string('store');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('ios_usage_report');
    }
}
