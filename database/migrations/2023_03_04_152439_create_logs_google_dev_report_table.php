<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLogsGoogleDevReportTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
    Schema::drop('google_dev_report_logs');
        Schema::create('google_dev_report_logs', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('log_name');
            $table->string('api');
            $table->string('result');
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
        Schema::drop('google_dev_report_logs');
    }
}
