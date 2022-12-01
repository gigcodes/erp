<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            // $table->foreign('task_category_id')->references('id')->on('task_categories')->onDelete('cascade');

            $table->unsignedInteger('task_subcategory_id');
            // $table->foreign('task_subcategory_id')->references('id')->on('task_sub_categories')->onDelete('cascade');
            $table->string('name')->nullable();
            $table->string('description')->nullable();
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
