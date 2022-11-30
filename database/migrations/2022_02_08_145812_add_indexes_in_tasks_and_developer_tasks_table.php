<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexesInTasksAndDeveloperTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tasks', function (Blueprint $table) {
            //
            $table->index('is_flagged');
            $table->index('assign_to');
            $table->index('assign_from');
            $table->index('status');
        });
        Schema::table('developer_tasks', function (Blueprint $table) {
            $table->index('is_flagged');
            $table->index('task_type_id');
            $table->index('created_by');
            $table->index('responsible_user_id');
            $table->index('status');
            $table->index('master_user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tasks', function (Blueprint $table) {
            //
            $table->dropIndex('is_flagged');
            $table->dropIndex('assign_to');
            $table->dropIndex('assign_from');
            $table->dropIndex('status');
        });
        Schema::table('developer_tasks', function (Blueprint $table) {
            $table->dropIndex('is_flagged');
            $table->dropIndex('task_type_id');
            $table->dropIndex('created_by');
            $table->dropIndex('responsible_user_id');
            $table->dropIndex('status');
            $table->dropIndex('master_user_id');
        });
    }
}
