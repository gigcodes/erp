<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameProjectNameInMagentoCronListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('magento_cron_lists', function (Blueprint $table) {
            $table->renameColumn('project_name', 'website_ids');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('magento_cron_lists', function (Blueprint $table) {
            $table->renameColumn('website_ids', 'project_name');
        });
    }
}
