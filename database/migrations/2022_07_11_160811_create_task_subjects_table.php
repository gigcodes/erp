<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTaskSubjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('task_subjects', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('task_category_id');
            $table->foreign('task_category_id')->references('id')->on('task_categories')->onDelete('cascade');
            $table->unsignedInteger('task_subcategory_id');
            $table->foreign('task_subcategory_id')->references('id')->on('task_sub_categories')->onDelete('cascade');
            $table->string('name');
            $table->string('description');
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
        Schema::dropIfExists('task_subjects');
    }
}
