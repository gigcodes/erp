<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateIosAppRatingsReportTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
    
        Schema::create('ios_ratings_report', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('group_by');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('product_id');
              $table->string('breakdown');
        $table->string('new');
        $table->string('average');
        $table->integer('total');
        $table->string('new_average');
        $table->integer('new_total');
        $table->integer('positive');
        $table->integer('negative');
        $table->integer('neutral');
        $table->integer('new_positive');
        $table->integer('new_negative');
        $table->integer('new_neutral');
       
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
        Schema::drop('ios_ratings_report');
    }
}
