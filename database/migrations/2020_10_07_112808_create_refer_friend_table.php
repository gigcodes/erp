<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReferFriendTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('refer_friend', function (Blueprint $table) {
            $table->increments('id');
            $table->string('referrer_first_name')->nullable();
            $table->string('referrer_last_name')->nullable();
            $table->string('referrer_email')->nullable();
            $table->string('referrer_phone')->nullable();
            $table->string('referee_first_name')->nullable();
            $table->string('referee_last_name')->nullable();
            $table->string('referee_email')->nullable();
            $table->string('referee_phone')->nullable();
            $table->string('domain_name')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('refer_friend');
    }
}
