<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeveoperTaskPullRequestMergeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deveoper_task_pull_request_merges', function (Blueprint $table) {
            $table->increments('id');
            $table->string('task_id', 30);
            $table->integer('pull_request_id')->nullable();
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
        Schema::dropIfExists('deveoper_task_pull_request_merges');
    }
}
