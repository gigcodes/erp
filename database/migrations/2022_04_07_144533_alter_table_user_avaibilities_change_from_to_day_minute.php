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
        Schema::table('user_avaibilities', function($table){
            $table->date('from')->nullable()->change();
            $table->date('to')->nullable()->change();
            $table->integer('day')->default(0)->change();
            $table->time('minute')->change();
            $table->text('date')->change();
        });
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
