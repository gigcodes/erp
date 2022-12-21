<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterSyncStatusColumnToLogListMagentoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('log_list_magentos', function (Blueprint $table) {
            $table->enum('sync_status', ['success','error','waiting','started_push','size_chart_needed','image_not_found','translation_not_found','initialization','first_job_started','condition_checking','second_job_started','condition_true'])->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('log_list_magentos', function (Blueprint $table) {
            $table->enum('sync_status', ['success','error','waiting','started_push','size_chart_needed','image_not_found','translation_not_found'])->change();
        });
    }
}
