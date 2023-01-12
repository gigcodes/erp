<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TestCaseStatusHistoryNew extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('test_case_status_history', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('test_case_id');
            $table->integer('updated_by');
            $table->integer('new_status');
            $table->integer('old_status')->nullable();
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
        Schema::dropIfExists('test_case_status_history');
    }
}
