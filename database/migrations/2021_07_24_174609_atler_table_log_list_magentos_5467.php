<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AtlerTableLogListMagentos5467 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('log_list_magentos',function(Blueprint $table) {
            $table->timestamp('job_start_time')->nullable();
            $table->timestamp('job_end_time')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('log_list_magentos',function(Blueprint $table) {
            $table->dropField("job_start_time");
            $table->dropField("job_end_time");
        });
    }
}
