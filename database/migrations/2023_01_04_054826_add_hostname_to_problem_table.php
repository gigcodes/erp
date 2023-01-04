<?php

use DB;
use Illuminate\Database\Migrations\Migration;

class AddHostnameToProblemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE `problems` ADD `hostname` VARCHAR(200) NULL DEFAULT NULL AFTER `name`;');
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
