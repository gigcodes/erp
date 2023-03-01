<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateIosAppSalesReportTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
    
        Schema::create('ios_sales_report', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('group_by');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('product_id');
               $table->integer("downloads");
        $table->integer("re_downloads");
        $table->integer("uninstalls");
        $table->integer("updates");
        $table->integer("returns");
        $table->integer("net_downloads");
        $table->integer("promos");
        $table->string("revenue");
        $table->string("returns_amount");
        $table->integer("edu_downloads");
        $table->integer("gifts");
        $table->integer("gift_redemptions");
        $table->string("edu_revenue");
        $table->string("gross_revenue");
        $table->string("gross_returns_amount");
        $table->string("gross_edu_revenue");
        $table->integer("business_downloads");
        $table->string("business_revenue");
        $table->string("gross_business_revenue");
        $table->integer("standard_downloads");
        $table->string("standard_revenue");
        $table->string("gross_standard_revenue");
        $table->integer("app_downloads");
        $table->integer("app_returns");
        $table->integer("iap_amount");
        $table->integer("iap_returns");
        $table->integer("subscription_purchases");
        $table->integer("subscription_returns");
        $table->string("app_revenue");
        $table->string("app_returns_amount");
        $table->string("gross_app_revenue");
        $table->string("gross_app_returns_amount");
        $table->string("iap_revenue");
        $table->string("iap_returns_amount");
        $table->string("gross_iap_revenue");
        $table->string("gross_iap_returns_amount");
        $table->string("subscription_revenue");
        $table->string("subscription_returns_amount");
        $table->string("gross_subscription_revenue");
        $table->integer("gross_subscription_returns_amount");
        $table->integer("pre_orders");

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
        Schema::drop('ios_sales_report');
    }
}
