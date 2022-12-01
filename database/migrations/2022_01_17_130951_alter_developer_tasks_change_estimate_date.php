<?php

use Illuminate\Database\Migrations\Migration;

class AlterDeveloperTasksChangeEstimateDate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE developer_tasks MODIFY COLUMN estimate_date DATETIME');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE developer_tasks MODIFY COLUMN estimate_date DATE');
    }
}
