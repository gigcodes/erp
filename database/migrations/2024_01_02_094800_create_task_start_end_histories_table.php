<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaskStartEndHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('task_start_end_histories', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->default(0);
            $table->integer('task_id')->default(0);
            $table->string('start_date')->nullable();
            $table->string('end_date')->nullable();
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
        Schema::dropIfExists('task_start_end_histories');
    }
}
