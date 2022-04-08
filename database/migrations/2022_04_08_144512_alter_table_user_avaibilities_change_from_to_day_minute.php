<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
class UserAvaibilitiesChangeFromToDayMinute extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE `user_avaibilities` MODIFY
        CHANGE `from` `from` DATE NULL DEFAULT NULL,
        CHANGE `to` `to` DATE NULL DEFAULT NULL,
        CHANGE `minute` `minute` TIME NULL DEFAULT NULL,
        CHANGE `date` `date` TEXT NULL DEFAULT NULL, 
        CHANGE `day` `day` INTEGER NULL DEFAULT NULL");
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
