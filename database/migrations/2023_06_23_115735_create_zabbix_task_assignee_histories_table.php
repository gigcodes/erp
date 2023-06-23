<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateZabbixTaskAssigneeHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zabbix_task_assignee_histories', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer("zabbix_task_id");
            $table->integer("old_assignee")->nullable();
            $table->integer("new_assignee")->nullable();
            $table->integer("user_id");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('zabbix_task_assignee_histories');
    }
}
