<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableLogListMagentoSizeChartUrl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('log_list_magentos',function(Blueprint $table) {
            $table->string("size_chart_url")->nullable()->after('queue_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('log_list_magentos',function(Blueprint $table) {
            $table->dropField("size_chart_url");
        });   
    }
}
