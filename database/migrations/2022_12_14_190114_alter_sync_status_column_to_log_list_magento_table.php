<?php

use Doctrine\DBAL\Types\Types;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AlterSyncStatusColumnToLogListMagentoTable extends Migration
{
    public function __construct()
    {
        DB::getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', Types::STRING);
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::table('log_list_magentos', function (Blueprint $table) {
        //     $table->enum('sync_status', ['success','error','waiting','started_push','size_chart_needed','image_not_found','translation_not_found','initialization','first_job_started','condition_checking','second_job_started','condition_true'])->change();
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::table('log_list_magentos', function (Blueprint $table) {
        //     $table->enum('sync_status', ['success','error','waiting','started_push','size_chart_needed','image_not_found','translation_not_found'])->change();
        // });
    }
}
