<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateIosAppAdsReportTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
    
        Schema::create('ios_ads_report', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('networks');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('product_id');
               $table->string("revenue");
        $table->integer("requests");
        $table->integer("impressions");
        $table->string("ecpm");
        $table->string("fillrate");
        $table->string("ctr");
        $table->integer("clicks");
        $table->integer("requests_filled");
      
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('ios_ads_report');
    }
}
