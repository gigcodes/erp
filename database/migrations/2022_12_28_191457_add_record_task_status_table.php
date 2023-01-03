<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddRecordTaskStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        DB::statement('INSERT INTO `task_statuses` (`id`, `name`, `created_at`, `updated_at`) VALUES (22, "Done", "2022-12-28 00:00:00", "2022-12-28 00:00:00");');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
