<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTestCaseUserHistoriesTableNew extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('test_case_user_histories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('test_case_id');
            $table->integer('updated_by');
            $table->integer('new_user');
            $table->integer('old_user')->nullable();
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
        Schema::dropIfExists('test_case_user_histories');
    }
}