<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddInstaAccAutomationFormTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         //
         Schema::create('insta_acc_automation_form',function(Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('posts_per_day')->default(1);
            $table->tinyInteger('likes_per_day')->default(1);
            $table->tinyInteger('send_requests_per_day')->default(1);
            $table->tinyInteger('accept_requests_per_day')->default(1);
            $table->timestamps();
        });
        
        DB::table('insta_acc_automation_form')->insert([
            'posts_per_day' => 1,
            'likes_per_day' => 1,
            'send_requests_per_day' => 1,
            'accept_requests_per_day' => 1,
        ]);
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
