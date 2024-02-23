<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStartEndTimeToDeveloperTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('developer_tasks', function (Blueprint $table) {
            $table->string('m_start_date')->after('task_start')->nullable();
            $table->string('m_end_date')->after('m_start_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('developer_tasks', function (Blueprint $table) {
            //
        });
    }
}
