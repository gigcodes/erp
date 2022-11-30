<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableDeveloperTaskSendRemiderTime extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('developer_tasks', function (Blueprint $table) {
            $table->timestamp('last_send_reminder')->after('frequency');
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
        Schema::table('developer_tasks', function (Blueprint $table) {
            $table->dropField('last_send_reminder');
        });
    }
}
