<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableProjectFileManagersAddField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_file_managers', function (Blueprint $table) {
            $table->integer('display_dev_master')->default(0)->after('notification_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('project_file_managers', function (Blueprint $table) {
            $table->dropField('display_dev_master');
        });
    }
}
