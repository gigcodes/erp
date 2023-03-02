<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateIosAppPaymentsReportTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
    
        Schema::create('ios_payments_report', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('group_by');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('product_id');
              $table->float('revenue');
        $table->float('converted_revenue');
        $table->float('financial_revenue');
        $table->float('estimated_revenue');
        
       
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
        Schema::drop('ios_payments_report');
    }
}
