<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UserAvaibilitiesChangeFromToDayMinute extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        \DB::statement("ALTER TABLE `user_avaibilities` CHANGE `from` `from`
        DATE NULL DEFAULT NULL,
        CHANGE `to` `to`
        DATE NULL DEFAULT NULL,
        CHANGE `minute` `minute`
        TIME NULL DEFAULT NULL,
        CHANGE `date` `date` TEXT NULL DEFAULT NULL,
        CHANGE `day` `day` INTEGER NULL DEFAULT NULL");
    
        // Schema::table('user_avaibilities', function($table){
        //     $table->date('from')->nullable()->change();
        //     $table->date('to')->nullable()->change();
        //     $table->integer('day')->default(0)->change();
        //     $table->time('minute')->change();
        //     $table->text('date')->change();
        // });
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
