<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddEndPointToPostmanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE `postman_request_creates` ADD `end_point` TEXT NULL DEFAULT NULL AFTER `json_body_id`;');
        DB::statement('ALTER TABLE `postman_edit_histories` ADD `end_point` TEXT NULL DEFAULT NULL AFTER `tests`;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        
    }
}
