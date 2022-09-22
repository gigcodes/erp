<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserAvaibilityHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_avaibility_histories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_avaibility_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->string("from")->nullable();
            $table->string("to")->nullable();
            $table->string("status")->nullable();
            $table->string("note")->nullable();
            $table->longText("date")->nullable();
            $table->string("start_time")->nullable();
            $table->string("end_time")->nullable();
            $table->string("lunch_time")->nullable();
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
        Schema::dropIfExists('user_avaibility_histories');
    }
}
